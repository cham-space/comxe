<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/3
 * Time: 下午5:57
 */

namespace Gomeplus\Comx\Schema\OnError;


use Gomeplus\Comx\Debug\DebugTrait;

class IgnoreStrategy extends Strategy
{
    use DebugTrait;

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
        $this->appendDebugInfo('Ignored Exception', ['exception'=>$ex->getMessage(), 'class'=>get_class($ex)]);
        return null;
    }

    public function handleDecorException(\Exception $ex, &$data)
    {
        $this->appendDebugInfo('Ignored Exception', ['exception'=>$ex->getMessage(), 'class'=>get_class($ex)]);
        return;
    }
}