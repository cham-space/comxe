<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/18
 * Time: 上午1:09
 */

namespace Gomeplus\Comx\Context\SourceBase;

use Comos\Config\Config;
use Gomeplus\Comx\Context\Context;
use Gomeplus\Comx\Debug\DebugTrait;
use Gomeplus\Comx\Log\LogTrait;

abstract class SourceBaseAbstract
{
    use DebugTrait;

    use LogTrait;

    const FIELD_ID = 'id';
    /**
     * @var Config
     */
    protected $conf;

    /**
     * HttpSourceBase constructor.
     * @param Config $conf
     */
    public function __construct(Config $conf)
    {
        $this->conf = $conf;
    }

    public function getId()
    {
        return $this->conf->rstr(self::FIELD_ID);
    }

    abstract public function executeLoading(Context $context, Config $sourceOptions, $reservedVariablesForUriTemplate);
}