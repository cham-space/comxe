<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/3/29
 * Time: 下午9:12
 */

namespace Gomeplus\Comx;

use Gomeplus\Comx\Cache\RedisCache;
use Gomeplus\Comx\Context\Context;
use Gomeplus\Comx\Context\SourceBase\SourceBaseFactory;
use Gomeplus\Comx\Debug\Debugger;
use Gomeplus\Comx\Debug\DebugTrait;
use Gomeplus\Comx\Log\LoggerFactory;
use Gomeplus\Comx\Log\LogTrait;
use Gomeplus\Comx\Rest\Client;
use Gomeplus\Comx\Rest\HttpResponseException;
use Gomeplus\Comx\Rest\RequestMessage;
use Gomeplus\Comx\Rest\ResponseMessage;
use Gomeplus\Comx\Schema\Loader;
use Gomeplus\Comx\Schema\ScriptLoader\ScriptLoader;

class Bootstrap
{
    use LogTrait;

    use DebugTrait;

    const CONF_FILENAME = 'comx.conf.json';
    /**
     * @var string
     */
    protected $home;
    /**
     * @var \Comos\Config\Config
     */
    protected $conf;

    public function __construct($pathToHome)
    {
        if (!is_dir($pathToHome)) {
            throw new \Exception('COMX_HOME dir is not valid. ' . $pathToHome);
        }
        $this->home = $pathToHome;

        $confFile = $this->home . DIRECTORY_SEPARATOR . self::CONF_FILENAME;
        $this->conf = \Comos\Config\Loader::fromJsonFile($confFile);
        $this->initLog();
        $this->initDebugger();
    }

    protected function initLog()
    {
        $logConf = $this->conf->sub('log');
        if (!$logConf->bool('enabled', false)) {
            return;
        }
        $dir = realpath($logConf->rstr('dir'));
        assert(!empty($dir));
        LoggerFactory::setLogger(
            LoggerFactory::createLogger($dir, $logConf->bool('debug', false))
        );
    }

    protected function initCache()
    {
        $cacheConf = $this->conf->sub('cache')->sub('redis');
        $cache = RedisCache::fromConf($cacheConf);
        return $cache;
    }

    protected function initDebugger()
    {
        $debugEnabled = $this->conf->bool('debug', false);
        if ($debugEnabled) {
            Debugger::enable();
        }
    }


    public function start()
    {
        $startTime = microtime(true);
        $conf = $this->conf;
        $response = null;
        try {
            //从home 指定位置加载配置文件

            //初始化context
            $context = new Context();
            $request = RequestMessage::fromEnv();
            $cache = self::initCache();
            $schemaLoader = new Loader($this->home);
            $context->setSchemaLoader($schemaLoader)
                ->setUrlPrefix($conf->rstr('urlPrefix'))
                ->setSourceBaseFactory(SourceBaseFactory::fromConf($conf))
                ->setRequest($request)
                ->setScriptLoader(new ScriptLoader($this->home))
                ->setCache($cache)
                ->setRestClient(new Client());

            if ($request->getUrl()->getQuery()->getParameter('__refresh') == 1) {
                $cache->enableRefreshing();
            }

            //执行
            $_ms = null;
            if ($this->conf->bool('debug') && preg_match('/\\/_comx\\/meta\\/{0,1}$/', $request->getUrl()->getPath(), $_ms)) {
                $handler = new MetaHandler();
            } else {
                $handler = new Handler();
            }

            $handler->handle($context);
            $response = $context->getResponse();
        } catch (HttpResponseException $ex) {
            $response = new ResponseMessage(null, $ex->getUserMessage(), $ex->getCode());
            $this->logger()->error('', ['exception'=>$ex]);
            $this->appendDebugInfo('', ['exception'=>strval($ex)]);
        } catch (\Exception $ex) {
            $this->logger()->error('', ['exception'=>$ex]);
            $this->appendDebugInfo('', ['exception'=>strval($ex)]);
            $response = new ResponseMessage(null, "服务暂不可用,请稍后再试.", 500);
        }

        $this->appendDebugInfo('EXEC TIME:'.(microtime(true)-$startTime));
        if (Debugger::isEnabled()) {
            $response->setDebugInfo(Debugger::getLogString());
        }
        $response->send();
    }
}