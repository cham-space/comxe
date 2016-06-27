<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/22
 * Time: 下午5:31
 */

namespace Gomeplus\Comx\Log;

use Psr\Log\LoggerInterface;

trait LogTrait
{
    /**
     * @return LoggerInterface
     */
    public static function logger()
    {
        return LoggerFactory::getLogger();
    }
}