<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/24
 * Time: 下午5:39
 */

namespace Gomeplus\Comx\Schema\DataDecor;

use Gomeplus\Comx\Context\Context;
use Gomeplus\Comx\Debug\DebugTrait;
use Gomeplus\Comx\Log\LogTrait;

class FixedDecor extends AbstractDecor
{
    const FIELD_FIXED_DATA = 'fixedData';
    const FIELD_FIELD = 'field';
    use DebugTrait;
    use LogTrait;
    /**
     * @param array &$data
     * @param Context $context
     * @return void
     */
    public function doDecorate(&$data, Context $context)
    {
        $loaded = $this->conf->rsub(self::FIELD_FIXED_DATA)->rawData();
        $field = $this->conf->str(self::FIELD_FIELD);
        if ($field) {
            $loaded = [$field=>$loaded];
        }
        $data = array_merge($data, $loaded);
    }

    public function getType()
    {
        return AbstractDecor::TYPE_FIXED;
    }
}