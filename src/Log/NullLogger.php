<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/22
 * Time: 下午9:23
 */

namespace Gomeplus\Comx\Log;


use Psr\Log\AbstractLogger;

class NullLogger extends AbstractLogger
{


    public function log($level, $message, array $context = array())
    {
        is_null($level);
        is_null($message);
        is_null($context);
    }
}