<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/11
 * Time: 上午11:55
 */

namespace Gomeplus\Comx\Schema\DataDecor;

use Gomeplus\Comx\Context\Context;

class NullDecorCache extends DecorCache
{
    public function __construct()
    {
        parent::__construct('', 0, false);
    }
    
    public function setAfterChildren(Context $context, $data)
    {
        is_null($context);
        is_null($data);
    }

    public function setBeforeChildren(Context $context, $data)
    {
        is_null($context);
        is_null($data);
    }

    public function getWithChildren(Context $context)
    {
        is_null($context);
        return null;
    }

    public function getWithoutChildren(Context $context)
    {
        is_null($context);
        return null;
    }
}