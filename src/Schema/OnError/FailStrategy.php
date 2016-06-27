<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/3
 * Time: 下午5:58
 */

namespace Gomeplus\Comx\Schema\OnError;


class FailStrategy extends Strategy
{
    private static $inst;
    /**
     * @return FailStrategy
     */
    public static function getInstance()
    {
        if (self::$inst)
        {
            return self::$inst;
        }

        return self::$inst = new self();
    }
    
    public function handleSourceException(\Exception $ex)
    {
        throw $ex;
    }

    public function handleDecorException(\Exception $ex, &$data)
    {
        is_null($data);
        throw $ex;
    }
}