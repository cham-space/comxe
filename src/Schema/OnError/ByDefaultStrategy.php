<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/3
 * Time: 下午5:58
 */

namespace Gomeplus\Comx\Schema\OnError;


use Comos\Config\Config;
use Gomeplus\Comx\Debug\DebugTrait;
use Gomeplus\Comx\Log\LogTrait;

class ByDefaultStrategy extends Strategy
{

    const DEFAULT_VALUE_FIELD = 'defaultValue';

    use LogTrait;

    use DebugTrait;

    protected $conf;

    public function __construct(Config $conf)
    {
        $this->conf = $conf;
    }

    public function handleSourceException(\Exception $ex)
    {
        $this->logger()->error('Caught exception, return value by default', ['exception'=>$ex]);
        return $this->conf->rsub(self::DEFAULT_VALUE_FIELD)->rawData();
    }
    
    public function handleDecorException(\Exception $ex, &$data)
    {
        is_null($data);
        throw new UnsupportedStrategyException('the onError strategy "byDefault" is not available on Decor node', null, $ex);
    }
}