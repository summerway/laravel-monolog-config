<?php
/**
 * Created by PhpStorm.
 * User: Maple.xia
 * Date: 2019/1/22
 * Time: 9:57 AM
 */

namespace MapleSnow\MonologConfig\Formatter;

use Monolog\Formatter\HtmlFormatter;
use Exception;

/**
 * Class RedisFormatter
 * @package MapleSnow\MonologConfig\Formatter
 */
class MailFormatter extends HtmlFormatter {

    /**
     * @param array $record
     * @return array|mixed|string
     */
    public function format(array $record)
    {
        $output = $this->addTitle($record['level_name'], $record['level']);
        $output .= '<table cellspacing="1" width="100%" class="monolog-output">';

        $output .= $this->addRow('Time', $record['datetime']->format($this->dateFormat));
        $output .= $this->addRow('Host', request()->getHost());
        $output .= $this->addRow('remoteAddress', request()->getClientIp());

        $message = (string)$record['message'];
        // exception info
        if(isset($record['context']['exception']) && $record['context']['exception'] instanceof Exception){
            /** @var Exception $e */
            $e = $record['context']['exception'];
            $file = $e->getFile();
            $line = $e->getLine();
            $code = $e->getCode();

            $exception = compact('file','line','code','message');
            $embeddedTable = '<table cellspacing="1" width="100%">';
            foreach ($exception as $key => $value) {
                $embeddedTable .= $this->addRow($key, $this->convertToString($value));
            }
            $embeddedTable .= '</table>';
            $output .= $this->addRow('Exception', $embeddedTable, false);
        }else{
            $output .= $this->addRow('Message', $message);
        }

        // customer input
        if($extra= $record['context']['extra'] ?? ""){
            $output .= $this->addRow('Extra', $this->convertToString($extra));
        }

        // request info
        $input = request()->all();
        if($input){
            $url = request()->getUri();
            $request  = compact('url','input');
            $embeddedTable = '<table cellspacing="1" width="100%">';
            foreach ($request as $key => $value) {
                $embeddedTable .= $this->addRow($key, $this->convertToString($value));
            }
            $embeddedTable .= '</table>';
            $output .= $this->addRow('Request', $embeddedTable, false);
        }

        return $output.'</table>';
    }
}