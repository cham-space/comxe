<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/24
 * Time: 下午6:57
 */

namespace Gomeplus\Comx\Schema\DataDecor;


use Gomeplus\Comx\Context\Context;

class CompositionDecor extends AbstractDecor
{
    public function doDecorate(&$data, Context $context)
    {
    }

    public function getType()
    {
        return AbstractDecor::TYPE_COMPOSITION;
    }
}