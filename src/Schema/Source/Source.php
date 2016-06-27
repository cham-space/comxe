<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/24
 * Time: 下午5:57
 */

namespace Gomeplus\Comx\Schema\Source;

use Comos\Config\Config;
use Gomeplus\Comx\Context\Context;
use Gomeplus\Comx\Context\SourceBase\SourceBaseAbstract;
use Gomeplus\Comx\Debug\DebugTrait;
use Gomeplus\Comx\Log\LogTrait;
use Gomeplus\Comx\Schema\OnError\Strategy;
use Peekmo\JsonPath\JsonStore;

class Source
{
    const DEFAULT_TIMEOUT = 8000;
    const FIELD_TIMEOUT = 'timeout';
    const FIELD_BASE = 'base';
    const FIELD_URI = 'uri';
    const FIELD_CACHE = 'cache';
    const RESERVED_TPL_VAR_REQUEST = 'request';
    const FIELD_FIRST_ENTRY_ONLY = 'firstEntryOnly';
    const FIELD_METHOD = 'method';
    const FIELD_JSON_PATH = 'jsonPath';
    const FIELD_ON_ERROR = 'onError';
    const FIELD_BACKUP = 'backup';

    /**
     * @var Config
     */
    protected $conf;

    use LogTrait;

    use DebugTrait;


    /**
     * Source constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->conf = $config;
    }
    /**
     * @param Context $context
     * @param $reservedVariablesForUriTemplate
     * @return array|mixed
     * @throws UnmatchedRequestMethodException
     * @throws \Comos\Config\Exception
     */
    public function loadData(Context $context, $reservedVariablesForUriTemplate)
    {
        $ex = null;
        $result = null;
        try {
            $result = $this->doLoadData($context, $reservedVariablesForUriTemplate);
        } catch (\Exception $e) {
            $ex = $e;
        }
        // everything is OK
        if (!is_null($result) && is_null($ex)) {
            return $result;
        }

        $backupConf = $this->conf->sub(self::FIELD_BACKUP);
        
        // null or exception without backup
        if (empty($backupConf->rawData())) {
            if ($ex) {
                throw $ex;
            }

            if (is_null($result)) {
                return null;
            }
        }
        
        // active the backup
        $backupSource = new Source($backupConf);
        return $backupSource->loadData($context, $reservedVariablesForUriTemplate);
    }


    /**
     * @param $data
     * @return array|null
     * @throws \Comos\Config\Exception
     */
    protected function extractDataWithJsonPath($data)
    {
        $store = new JsonStore();
        $matchedData = $store->get($data, $this->conf->str(self::FIELD_JSON_PATH));

        if ($this->conf->bool(self::FIELD_FIRST_ENTRY_ONLY, false)) {
            return isset($matchedData[0]) ? $matchedData[0] : null;
        }
        return $matchedData;
    }

    /**
     * @param Context $context
     * @return SourceBaseAbstract
     * @throws \Comos\Config\Exception
     * @throws \Gomeplus\Comx\Context\SourceBase\UndefinedSourceBaseException
     */
    protected function getBase(Context $context)
    {
        $baseId = $this->conf->str(self::FIELD_BASE);
        return $context->getSourceBaseFactory()->getSourceBase($baseId);
    }

    /**
     * @param Context $context
     * @param $reservedVariablesForUriTemplate
     * @return array|null
     * @throws \Gomeplus\Comx\Schema\OnError\UnknownOnErrorStrategyException
     */
    protected function doLoadData(Context $context, $reservedVariablesForUriTemplate)
    {
        try {
            $tplParams = array_merge($reservedVariablesForUriTemplate, [self::RESERVED_TPL_VAR_REQUEST => $context->getRequest()]);

            $defaultKeyTpl = $this->conf->rstr(self::FIELD_URI);

            $cache = SourceCache::create($this->conf->sub(self::FIELD_CACHE), $tplParams, $defaultKeyTpl);

            if ($data = $cache->get($context)) {
                return $data;
            }

            $data = $this->getBase($context)->executeLoading($context, $this->conf, $tplParams);

            $cache->set($context, $data);

            $jsonPath = $this->conf->str(self::FIELD_JSON_PATH);
            if (is_null($jsonPath)) {
                return $data;
            }

            return $this->extractDataWithJsonPath($data);
        } catch (\Exception $ex) {
            return Strategy::fromConf($this->conf->sub(Source::FIELD_ON_ERROR))->handleSourceException($ex);
        }
    }
}