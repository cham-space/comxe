<?php
namespace Gomeplus\Comx\Schema\DataDecor;
use Comos\Config\Config;
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/24
 * Time: 下午5:25
 */
class DecorFactory
{
    /**
     * @param Config $conf
     * @param string|null $forceType
     * @return AbstractDecor
     * @throws UnknownDecorTypeException
     * @throws \Comos\Config\Exception
     */
    public static function create(Config $conf, $forceType = null)
    {
        if ($forceType) {
            $type = $forceType;
        } else {
            $type = $conf->str(AbstractDecor::FIELD_TYPE, AbstractDecor::TYPE_EACH);
        }
        if (!in_array($type, AbstractDecor::ACCEPTED_TYPES)) {
            throw new UnknownDecorTypeException("unknown decor type. TYPE[$type]");
        }
        $qualifiedDecoClazz = __NAMESPACE__.'\\'.$type.'Decor';
        return new $qualifiedDecoClazz($conf);
    }
}