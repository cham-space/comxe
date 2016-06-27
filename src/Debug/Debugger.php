<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/3
 * Time: 下午8:35
 */

namespace Gomeplus\Comx\Debug;


class Debugger
{
    static private $enabled = false;

    static private $records = [];

    public static function appendDebugInfo($message, $data = [])
    {
        if(!self::$enabled)
        {
            return;
        }

        self::$records[] = self::recordToString((count(self::$records)+1), $message, $data);
    }

    public static function enable()
    {
        self::$enabled = true;
    }

    public static function disable()
    {
        self::$enabled = false;
    }

    public static function isEnabled()
    {
        return self::$enabled;
    }


    protected static function recordToString($sequenceNo, $message, $data = [])
    {
        $row = "STEP($sequenceNo)\tMSG($message) \r\n";
        if (!empty($data)) {
            $json = json_encode($data, JSON_UNESCAPED_UNICODE, 5);
            if ($json ===  false) {
                $row.='The instance cannot be serialized.';
            } else {
                $row.= "DATA($json)";
            }
        }
        return $row;
    }

    /**
     * @return string
     */
    public static function getLogString()
    {
        return "\t".join("\r\n\t", self::$records);
    }
}