<?php
namespace Gomeplus\Comx\Schema\DataDecor;
use Comos\Config\Config;
use Gomeplus\Comx\Context\Context;
use Gomeplus\Comx\Debug\DebugTrait;
use Gomeplus\Comx\Log\LogTrait;
use Gomeplus\Comx\Schema\OnError\Strategy;

/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/24
 * Time: 下午5:17
 */
abstract class AbstractDecor
{
    const FIELD_DECORS = 'decors';
    use LogTrait;

    use DebugTrait;
    const FIELD_TYPE = 'type';
    const ACCEPTED_TYPES = [
        AbstractDecor::TYPE_ROOT,
        AbstractDecor::TYPE_SCRIPT,
        AbstractDecor::TYPE_COMPOSITION,
        AbstractDecor::TYPE_EACH,
        AbstractDecor::TYPE_BATCH,
        AbstractDecor::TYPE_FIXED,
    ];
    const TYPE_COMPOSITION = 'Composition';
    const TYPE_FIXED = 'Fixed';
    const TYPE_ROOT = 'Root';
    const TYPE_BATCH = 'Batch';
    const TYPE_EACH = 'Each';
    const TYPE_SCRIPT = 'Script';

    /**
     * @var \Comos\Config\Config
     */
    protected $conf;

    /**
     * AbstractDecor constructor.
     * @param \Comos\Config\Config $conf
     */
    public function __construct(Config $conf)
    {
        $this->conf = $conf;

    }

    /**
     * @param $data
     * @param Context $context
     * @return mixed
     */
    abstract public function doDecorate(&$data, Context $context);

    /**
     * @return string
     */
    abstract public function getType();

    /**
     * @param $data
     * @param Context $context
     */
    public function decorate(&$data, Context $context)
    {
        $confData = $this->conf->rawData();
        unset($confData[self::FIELD_DECORS]);
        $this->appendDebugInfo("Execute Decor:".$this->getType(), $confData);

        try {
            $cache = DecorCache::fromConf($this->conf->sub('cache'), ['request'=>$context->getRequest(), 'data'=>$data], $context);
            $cached = $cache->getWithChildren($context);
            if (!is_null($cached)) {
                $data = $cached;
                return;
            }

            $cached = $cache->getWithoutChildren($context);
            if (!is_null($cached)) {
                $data = $cached;
                $this->executeChildDecors($data, $context);
                return;
            }

            $this->doDecorate($data, $context);
            $cache->setBeforeChildren($context, $data);

            $this->executeChildDecors($data, $context);
            $cache->setAfterChildren($context, $data);
        } catch (\Exception $ex) {
            Strategy::fromConf($this->conf->sub('onError'))->handleDecorException($ex, $data);
        }

    }

    /**
     * @param $data
     * @param Context $context
     * @throws UnknownDecorTypeException
     */
    protected function executeChildDecors(&$data, Context $context)
    {
        $isParallel = $this->conf->bool('isParallel', false);
        if ($isParallel) {
            $this->executeChildDecorsParallelly($data, $context);
        } else {
            $this->sequentialExecuteChildDecors($data, $context);
        }
    }

    protected function executeChildDecorsParallelly(&$data, $context) {
        $batch = new BatchExecutor($data, $context);
        $children = $this->conf->sub(self::FIELD_DECORS);
        foreach ($children->keys() as $key) {
            $conf = $children->sub($key);
            $decor = DecorFactory::create($conf);
            $batch->addDecor($decor);
        }
        $batch->execute();
    }
    /**
     * @param $data
     * @param Context $context
     * @throws UnknownDecorTypeException
     */
    protected function sequentialExecuteChildDecors(&$data, Context $context)
    {
        $children = $this->conf->sub(self::FIELD_DECORS);
        foreach ($children->keys() as $key) {
            $conf = $children->sub($key);
            $decor = DecorFactory::create($conf);
            $decor->decorate($data, $context);
        }
    }
}