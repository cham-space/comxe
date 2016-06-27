<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/3
 * Time: 上午9:23
 */

namespace Gomeplus\Comx\Schema\OnError;


use Comos\Config\Config;

abstract class Strategy
{
    /**
     * @param Config $config
     * @return Strategy
     * @throws UnknownOnErrorStrategyException
     * @throws \Comos\Config\Exception
     */
    public static function fromConf(Config $config)
    {
        $type = $config->str('type', 'fail');
        
        if ($type == 'ignore') {
            return IgnoreStrategy::getInstance();
        }

        if ($type == 'fail') {
            return FailStrategy::getInstance();
        }

        if ($type == 'byDefault') {
            return new ByDefaultStrategy($config);
        }

        throw new UnknownOnErrorStrategyException('unknown strategy. type: '.$type);
    }
    
    abstract public function handleSourceException(\Exception $ex);
    abstract public function handleDecorException(\Exception $ex, &$data);
}