<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/3/29
 * Time: 下午9:44
 */

namespace Gomeplus\Comx\Context;

use Comos\Config\Config;
use Gomeplus\Comx\Cache\Cache;
use Gomeplus\Comx\Context\SourceBase\HttpSourceBase;
use Gomeplus\Comx\Context\SourceBase\SourceBaseFactory;
use Gomeplus\Comx\Rest\Client;
use Gomeplus\Comx\Rest\ResponseMessage;
use Gomeplus\Comx\Rest\RequestMessage;
use Gomeplus\Comx\Schema\Loader;
use Gomeplus\Comx\Schema\ScriptLoader\ScriptLoader;

class Context
{
    /**
     * @var Client
     */
    private $restClient;
    /**
     * @var ResponseMessage
     */
    private $response;
    /**
     * @var Loader
     */
    private $schemaLoader;
    /**
     * @var string
     */
    private $urlPrefix;
    /**
     * @var SourceBaseFactory
     */
    private $sourceBaseFactory;
    /**
     * @var RequestMessage
     */
    private $request;
    /**
     * @var ScriptLoader
     */
    private $scriptLoader;
    /**
     * @var Cache
     */
    private $cache;

    public function __construct()
    {
        $this->response = new ResponseMessage();
    }

    /**
     * @param $request
     * @return Context
     */
    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return RequestMessage
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Loader $loader
     * @return Context $this
     */
    public function setSchemaLoader(Loader $loader)
    {
        $this->schemaLoader = $loader;
        return $this;
    }

    /**
     * @param ScriptLoader $loader
     * @return Context $this
     */
    public function setScriptLoader(ScriptLoader $loader)
    {
        $this->scriptLoader = $loader;
        return $this;
    }

    /**
     * @return ScriptLoader
     */
    public function getScriptLoader()
    {
        return $this->scriptLoader;
    }

    /**
     * @return Loader
     */
    public function getSchemaLoader()
    {
        return $this->schemaLoader;
    }

    /**
     * @param Client $client
     * @return Context
     */
    public function setRestClient(Client $client)
    {
        $this->restClient = $client;
        return $this;
    }

    /**
     * @return Client
     */
    public function getRestClient()
    {
        return $this->restClient;
    }

    /**
     * @return ResponseMessage
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param $urlPrefix
     * @return Context
     */
    public function setUrlPrefix($urlPrefix)
    {
        $this->urlPrefix = $urlPrefix;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrlPrefix()
    {
        return $this->urlPrefix;
    }

    /**
     * @param SourceBaseFactory $factory
     * @return Context $this
     */
    public function setSourceBaseFactory(SourceBaseFactory $factory)
    {
        $this->sourceBaseFactory = $factory;
        return $this;
    }

    public function getSourceBaseFactory()
    {
        return $this->sourceBaseFactory;
    }

    /**
     * @deprecated
     * @param $atomicUrlPrefix
     * @return Context $this
     */
    public function setAtomicUrlPrefix($atomicUrlPrefix)
    {
        $this->sourceBaseFactory = new SourceBaseFactory();
        $this->sourceBaseFactory->putSourceBase(
            new HttpSourceBase(Config::fromArray(['id'=>'default', 'urlPrefix'=>$atomicUrlPrefix]))
        );
        return $this;
    }


    /**
     * @param Cache $cache
     * @return Context $this
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
        return $this;
    }

    /**
     * @return Cache
     */
    public function getCache()
    {
        return $this->cache;
    }
}