<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/25
 * Time: 上午1:00
 */

namespace Gomeplus\Comx\Schema\DataDecor;

use Gomeplus\Comx\Context\Context;

class ScriptDecor extends AbstractDecor
{
    public function doDecorate(&$data, Context $context)
    {
        $scriptName = $this->conf->rstr('script');
        $callback = $context->getScriptLoader()->load($scriptName);
        $callback($data, $context);
    }

    public function getType()
    {
        return AbstractDecor::TYPE_SCRIPT;
    }
}