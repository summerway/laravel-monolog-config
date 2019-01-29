<?php
/**
 * Created by PhpStorm.
 * User: Maple.xia
 * Date: 2019/1/28
 * Time: 4:41 PM
 */

namespace MapleSnow\MonologConfig\Handler;

use Illuminate\Support\Str;
use MapleSnow\MonologConfig\Formatter\RedisFilterFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Predis\Client;
use Redis;

/**
 * Logs to a Redis key depend on date and level
 *
 * usage example:
 *
 *   $log = new Logger('application');
 *   $redis = new RedisHandler(new Predis\Client("tcp://localhost:6379"), "logs", "prod");
 *   $log->pushHandler($redis);
 */
class RedisFilterHandler extends AbstractProcessingHandler
{
    private $redisClient;
    private $redisExpire;
    protected $capSize;

    /**
     * @param Client||Redis         redis   The redis instance
     * @param int|bool                   expire  The redis key expire time
     * @param int                   level   The minimum logging level at which this handler will be triggered
     * @param bool                  bubble  Whether the messages that are handled can bubble up the stack or not
     * @param int|bool                   $capSize Number of entries to limit list size to
     */
    public function __construct($redis, $expire = false, $level = Logger::DEBUG, $bubble = true, $capSize = false)
    {
        if (!(($redis instanceof Client) || ($redis instanceof Redis))) {
            throw new \InvalidArgumentException('Predis\Client or Redis instance required');
        }

        $this->redisClient = $redis;
        $this->redisExpire = $expire;
        $this->capSize = $capSize;

        parent::__construct($level, $bubble);
    }

    /**
     * {@inheritDoc}
     */
    protected function write(array $record)
    {
        if ($this->capSize) {
            $this->writeCapped($record);
        } else {
            $redisKey = date('Y-m-d').":".Str::lower($record['level_name'])."_log";
            $this->redisClient->rpush($redisKey, $record["formatted"]);
            if($this->redisExpire){
                $this->redisClient->expire($redisKey,$this->redisExpire);
            }
        }
    }

    /**
     * Write and cap the collection
     * Writes the record to the redis list and caps its
     *
     * @param  array $record associative record array
     * @return void
     */
    protected function writeCapped(array $record)
    {
        $redisKey = date('Y-m-d').":".Str::lower($record['level_name'])."_log";
        if ($this->redisClient instanceof Redis) {
            $this->redisClient->multi()
                ->rpush($redisKey, $record["formatted"])
                ->ltrim($redisKey, -$this->capSize, -1)
                ->exec();
        } else {
            $capSize = $this->capSize;
            $this->redisClient->transaction(function ($tx) use ($record, $redisKey, $capSize) {
                $tx->rpush($redisKey, $record["formatted"]);
                $tx->ltrim($redisKey, -$capSize, -1);
            });
        }

        if($this->redisExpire){
            $this->redisClient->expire($redisKey,$this->redisExpire);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultFormatter()
    {
        return new RedisFilterFormatter();
    }
}