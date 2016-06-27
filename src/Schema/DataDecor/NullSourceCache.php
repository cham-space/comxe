<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/9
 * Time: 下午9:57
 */

namespace Gomeplus\Comx\Schema\DataDecor;


use Gomeplus\Comx\Context\Context;
use Gomeplus\Comx\Schema\Source\SourceCache;

class NullSourceCache extends SourceCache
{
    public function __construct()
    {
        parent::__construct(null, 0);
    }

    public function set(Context $context, $data)
    {
        is_null($context);
        is_null($data);
    }

    public function get(Context $context)
    {
        is_null($context);
        return null;
    }

}