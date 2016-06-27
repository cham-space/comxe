<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/9
 * Time: 上午9:06
 */

namespace Gomeplus\Comx\Cache;

interface Cache
{
    public function get($key);
    
    public function set($key, $value, $timeoutMs);
}