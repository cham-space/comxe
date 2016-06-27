<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/22
 * Time: 下午5:33
 */

namespace Gomeplus\Comx\Log;


use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LoggerFactory
{
    protected static $logger;

    public static function getLogger()
    {
        if (!self::$logger) {
            self::$logger = self::createNullLogger();
        }
        return self::$logger;
    }

    public static function setLogger($logger)
    {
        self::$logger = $logger;
    }

    protected static function createNullLogger()
    {
        return new NullLogger();
    }

    public static function createLogger($logDirectory, $debugEnabled = false)
    {
        $monoLogger = new Logger('comx');
        $level = Logger::INFO;
        if ($debugEnabled) {
            $level = Logger::DEBUG;
        }

        $monoLogger->pushHandler(
            new StreamHandler($logDirectory.DIRECTORY_SEPARATOR.'comx.log', $level)
        );
        return $monoLogger;
    }
}