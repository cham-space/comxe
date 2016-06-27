<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/21
 * Time: 下午4:06
 */

namespace Gomeplus\Comx\Context;

use Comos\Config\Config;
use Gomeplus\Comx\Context\SourceBase\SourceBaseFactory;
use Gomeplus\Comx\Rest\Client;
use Gomeplus\Comx\Rest\ResponseMessage;
use Gomeplus\Comx\Schema\Loader;
use Gomeplus\Comx\Schema\ScriptLoader\ScriptLoader;

class ContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Context
     */
    protected $theContext;
    public function setUp()
    {
        parent::setUp();
        $this->theContext = new Context();
    }

    public function testSettersAndGetters()
    {
        /** @noinspection PhpDeprecationInspection */
        $val = $this->theContext->setAtomicUrlPrefix("http://aaa.com/v2");
        $this->assertTrue($val === $this->theContext);
        $this->assertEquals("http://aaa.com.cn/v2", $this->theContext->setUrlPrefix("http://aaa.com.cn/v2")->getUrlPrefix());

        $schemaLoader = new Loader('x');
        $this->assertTrue($schemaLoader === $this->theContext->setSchemaLoader($schemaLoader)->getSchemaLoader());

        $this->assertInstanceOf(ResponseMessage::class, $this->theContext->getResponse());
        $this->assertTrue($this->theContext->getResponse() ===  $this->theContext->getResponse());

        $scriptLoader = new ScriptLoader('x');
        $this->assertTrue($this->theContext->setScriptLoader($scriptLoader)->getScriptLoader() === $scriptLoader);

        $request = new ResponseMessage();
        $this->assertTrue($request === $this->theContext->setRequest($request)->getRequest());

        $client = new Client();
        $this->assertTrue($client ===  $this->theContext->setRestClient($client)->getRestClient());
    }

    public function testSetSourceBaseFactory()
    {
        $factory = SourceBaseFactory::fromConf(Config::fromArray([
            'atomicUrlPrefix' => 'http://a.com',
            'sourceBases' => [
                [
                    'id' => 'a',
                    'urlPrefix' => 'b',
                ]
            ]
        ]));
        $got = $this->theContext
            ->setSourceBaseFactory($factory)
            ->getSourceBaseFactory();

        $this->assertTrue($got === $factory);
    }
}
