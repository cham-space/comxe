<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/3
 * Time: 下午7:24
 */

namespace Gomeplus\Comx\Debug;


trait DebugTrait
{
    public function appendDebugInfo($message, $data=[])
    {
        Debugger::appendDebugInfo($message, $data);
    }

}