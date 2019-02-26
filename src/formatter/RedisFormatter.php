<?php
/**
 * Created by PhpStorm.
 * User: Maple.xia
 * Date: 2019/1/22
 * Time: 9:57 AM
 */

namespace MapleSnow\MonologConfig\Formatter;

use Monolog\Formatter\JsonFormatter as BaseJsonFormatter;
use Exception;

/**
 * Class RedisFormatter
 * @package MapleSnow\MonologConfig\Formatter
 */
class RedisFormatter extends BaseJsonFormatter {

    /**
     * {@inheritdoc}
     */
    public function format(array $record)
    {
        $level = $record['level_name'];
        $time = $record['datetime']->format('Y-m-d H:i:s');
        $server = [
            'host' => gethostname(),
            'hostName' => request()->getHost(),
            'address' => request()->server('SERVER_ADDR')
        ];

        $clientIp = request()->getClientIp();
        $message = $record['message'];

        // exception info
        if(isset($record['context']['exception']) && $record['context']['exception'] instanceof Exception){
            /** @var Exception $e */
            $e = $record['context']['exception'];
            $file = $e->getFile();
            $line = $e->getLine();
            $code = $e->getCode();

            $exception = compact('file','line','code','message');

            $data = compact('level','time','clientIp','server','exception');
        }else{
            $data = compact('level','time','clientIp','server','message');
        }

        // customer input
        if($extra= $record['context']['extra'] ?? ""){
            $data['extra'] = $extra;
        }

        // request info
        $input = request()->all();
        if($input){
            $url = request()->getUri();
            $data['request'] = compact('url','input');
        }

        $json = $this->toJson($this->normalize($data), true) . ($this->appendNewline ? "\n" : '');

        return $json;
    }
}