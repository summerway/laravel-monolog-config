<?php
/**
 * Created by PhpStorm.
 * User: Maple.xia
 * Date: 2019/1/22
 * Time: 4:02 PM
 */

namespace MapleSnow\MonologConfig;

use Monolog\Handler\AbstractHandler;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\FilterHandler;
use Monolog\Handler\RedisHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SwiftMailerHandler;
use Monolog\Handler\SyslogHandler;
use Monolog\Logger;
use Monolog\Formatter\FormatterInterface;
use Swift_Mailer;
use Swift_SmtpTransport;
use Swift_Message;
use Illuminate\Support\Str;
use Predis\Client;

class MonologConfigurator {

    protected $monolog;

    protected $config;

    public function __construct(Logger $monolog)
    {
        $this->monolog = $monolog;
        $this->config = config('logging');
    }

    public function run()
    {
        $fallback = true;
        foreach ($this->config['handlers'] as $config) {
            if (array_get($config, 'enabled', false)) {
                $fallback = $this->pushHandler($config['driver'], $config) ? false : $fallback;
            }
        }

        if ($fallback) {
            $handler = $this->config['fallback'];
            $config = array_get($this->config['handlers'], $handler);
            $this->pushHandler($config['driver'], $config);
        }
    }

    protected function pushHandler($handler, array $config)
    {
        $method = 'get'.Str::studly($handler).'Handler';
        if (method_exists($this, $method)) {
            try {
                $handler = $this->$method($config);
                if ($handler instanceof AbstractHandler) {
                    if(isset($config['formatter'])){
                        $formatter = new $config['formatter'];
                        if(!($formatter instanceof FormatterInterface)){
                            throw new \RuntimeException("It was not possible to create an instance.");
                        }

                        $handler->setFormatter($formatter);
                    }

                    $this->monolog->pushHandler($handler);
                }
                return true;
            } catch (\Exception $e) {
                throw new $e;
                //return false;
            }
        }
        return false;
    }

    /**
     * @param array $config
     * @return StreamHandler
     * @throws \Exception
     */
    protected function getStreamHandler(array $config)
    {
        return new StreamHandler($config['path'], $config['level']);
    }

    /**
     * @param array $config
     * @return RotatingFileHandler
     */
    protected function getRotatingFileHandler(array $config)
    {
        return new RotatingFileHandler($config['path'], $config['max_files'], $config['level']);
    }

    /**
     * @param array $config
     * @return SyslogHandler
     */
    protected function getSyslogHandler(array $config)
    {
        return new SyslogHandler($config['ident'], LOG_USER, $config['level']);
    }

    /**
     * @param array $config
     * @return ErrorLogHandler
     */
    protected function getErrorLogHandler(array $config)
    {
        return new ErrorLogHandler($config['message_type'], $config['level']);
    }

    /**
     * @param array $config
     * @return RedisHandler
     */
    protected function getRedisHandler(array $config)
    {
        $client = new Client([
            'scheme' => array_get($config,'scheme','tcp'),
            'host'   => array_get($config,'host','127.0.0.1'),
            'password'   => array_get($config,'password'),
            'port'   => array_get($config,'port',6379),
            'database' => array_get($config,'database',1)
        ]);

        $key = array_get($config,'key','log');
        $redisHandler =  new RedisHandler($client, $key, array_get($config,'level',Logger::DEBUG));

        $client->expire($key,array_get($config,'expire',43200));
        return $redisHandler;
    }

    /**
     * @param array $config
     * @return FilterHandler
     */
    protected function getRedisFilterHandler(array $config)
    {
        return new FilterHandler(function($record) use ($config){
            $client = new Client([
                'scheme' => array_get($config,'scheme','tcp'),
                'host'   => array_get($config,'host','127.0.0.1'),
                'password'   => array_get($config,'password'),
                'port'   => array_get($config,'port',6379),
                'database' => array_get($config,'database',1)
            ]);

            $key = date('Y-m-d').":".Str::lower($record['level_name'])."_log";
            $redisHandler =  new RedisHandler($client, $key, $config['level']);

            //filter special handle formatter
            if(isset($config['formatter'])){
                $formatter = new $config['formatter'];
                if(!($formatter instanceof FormatterInterface)){
                    throw new \RuntimeException("It was not possible to create an instance.");
                }

                $redisHandler->setFormatter($formatter);
            }

            $client->expire($key,$config['expire']);
            return $redisHandler;
        });
    }

    /**
     * @param array $config
     * @return SwiftMailerHandler
     */
    protected function getSwiftMailerHandler(array $config)
    {
        $transport = new Swift_SmtpTransport($config['host'],$config['port'],$config['encryption']);
        $transport->setUsername($config['username'])->setPassword($config['password']);

        // 创建mailer对象
        $mailer = new Swift_Mailer($transport);
        $message = new Swift_Message($config['subject']);
        $message->setFrom($config['from'])->setTo($config['to'])->setCc($config['cc']);

        return new SwiftMailerHandler($mailer, $message, array_get($config,'level',Logger::ERROR));
    }
}