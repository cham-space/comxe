<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/8
 * Time: 下午1:31
 */

namespace Gomeplus\Comx\Schema\Source;


use Comos\Config\Config;
use Gomeplus\Comx\Cache\Cache;
use Gomeplus\Comx\DecorTestBase;
use Gomeplus\Comx\Rest\RequestMessage;

class SourceTest extends DecorTestBase
{
    public function testGetData()
    {
        $conf = Config::fromArray([
            'uri' => '/echoService.php?a={pre.a}',
        ]);
        $source  = new Source($conf);
        $data = $source->loadData($this->comxContext, ['pre'=>['a'=>2]]);

        $this->assertEquals(2, $data['get']['a']);
        $this->assertEquals('GET', $data['server']['REQUEST_METHOD']);
    }

    public function testGetData_FromSpecificBase()
    {
        $conf = Config::fromArray([
            'uri' => '/itemService.php?id={pre.a}',
            'base' => 'sub',
        ]);
        $source  = new Source($conf);
        $data = $source->loadData($this->comxContext, ['pre'=>['a'=>2]]);
        $this->assertEquals(['id'=>2, 'title'=>'t2'], $data);

    }

    /**
     * @expectedException \Gomeplus\Comx\Rest\TimeoutException
     */
    public function testGetData_TimeoutByDefault()
    {
        //default timeout=8000 ms
        $conf = Config::fromArray(
            [
                'uri' => '/slowService.php?sleepingTime=9'
            ]
        );
        $source = new Source($conf);
        $source->loadData($this->comxContext, []);
    }

    /**
     * @expectedException \Gomeplus\Comx\Rest\TimeoutException
     */
    public function testGetData_Timeout()
    {
        $conf = Config::fromArray([
            'uri' => '/slowService.php?sleepTime=1',
            'timeout' => 50,//ms
        ]);
        $source = new Source($conf);
        $source->loadData($this->comxContext, []);
    }

    public function testGetData_Timeout_ByDefault()
    {
        $conf = Config::fromArray([
            'uri' => '/slowService.php?sleepTime=1',
            'timeout' => 50,//ms
            'onError' => [
                'type' => 'byDefault',
                'defaultValue' => ['x'=>'y'],
            ]
        ]);
        $source = new Source($conf);
        $data = $source->loadData($this->comxContext, []);
        $this->assertEquals(['x'=>'y'], $data);
    }

    /**
     * @expectedException \Gomeplus\Comx\Schema\Source\UnmatchedRequestMethodException
     */
    public function testGetData_UnmatchedRequestMethod()
    {
        $conf = Config::fromArray([
            'uri' => '/echoService.php',
            'method' => 'put',
        ]);
        $this->comxContext->setRequest(
            new RequestMessage('http://a.com/a', RequestMessage::METHOD_GET)
        );

        $source = new Source($conf);
        $source->loadData($this->comxContext, []);

    }

    public function testGetData_OnError_ByDefault()
    {
        $conf = Config::fromArray([
            'uri' => '/badService.php',
            'onError' => [
                'type' => 'byDefault',
                'defaultValue'=> ['d'=>3],
            ],
        ]);
        $source = new Source($conf);
        $data = $source->loadData($this->comxContext, []);
        $this->assertEquals(['d' => 3], $data);
    }

    public function testGetData_OnError_Ignore()
    {
        $conf = Config::fromArray([
            'uri' => '/badService.php',
            'onError' => [
                'type' => 'ignore',
            ],
        ]);
        $source = new Source($conf);
        $data = $source->loadData($this->comxContext, []);
        $this->assertNull($data);
    }

    /**
     * @expectedException \Gomeplus\Comx\Rest\MessageDecodingException
     * @expectedExceptionMessage invalid response with HTTP Code:200
     */
    public function testGetData_OnError_Fail()
    {
        $conf = Config::fromArray([
            'uri' => '/badService.php',
            'onError' => [
                'type' => 'fail',
            ],
        ]);
        $source = new Source($conf);
        $data = $source->loadData($this->comxContext, []);
        $this->assertNull($data);
    }

    public function testGetData_Post()
    {
        $contextRequest = new RequestMessage('http://a.com', RequestMessage::METHOD_POST, ['p'=>2]);

        $this->comxContext->setRequest($contextRequest);
        $conf = Config::fromArray([
            'uri' => '/echoService.php?a={pre.a}',
            'method' => 'post',
        ]);
        $source = new Source($conf);
        $data = $source->loadData($this->comxContext, ['pre'=>['a'=>3]]);

        $this->assertEquals(3, $data['get']['a']);
        $this->assertEquals(2, $data['body']['p']);
    }

    public function testGetData_WithCompletedUrl()
    {
        $conf = Config::fromArray(
            [
                'uri' => 'http://'.COMX_TEST_HOST.':'.COMX_TEST_PORT.'/echoService.php?x=3'
            ]
        );

        $source = new Source($conf);
        $data = $source->loadData($this->comxContext, []);

        $this->assertEquals('3', $data['get']['x']);
    }

    public function testGetData_WithJsonPath()
    {
        $conf = Config::fromArray([
            'uri' => '/echoService.php?v1={request.url.query.b}&v2={pre.b}',
            'jsonPath' => '$.get'
        ]);
        $source  = new Source($conf);
        $data = $source->loadData($this->comxContext, ['pre'=>['b'=>'2']]);
        $this->assertEquals([['v1'=>'1', 'v2'=>'2']], $data);
    }

    public function testGetData_WithJsonPath_FirstEntryOnly()
    {
        $conf = Config::fromArray([
            'uri' => '/echoService.php?v1={request.url.query.b}&v2={pre.b}',
            'jsonPath' => '$.get',
            'firstEntryOnly' => true,
        ]);
        $source  = new Source($conf);
        $data = $source->loadData($this->comxContext, ['pre'=>['b'=>'2']]);
        $this->assertEquals(['v1'=>'1', 'v2'=>'2'], $data);
    }

    public function testGetData_WithCache_Missed()
    {
        $conf = Config::fromArray([
            'uri' => '/userService.php?id={request.url.query.b}',
            'firstEntryOnly' => true,
            'cache' => [
                'ttl' => 10000,
                'key' => 'sourceCache',
            ]
        ]);

        $cacheMock = $this->getMockBuilder(Cache::class)
            ->setMethods(['set', 'get'])
            ->getMock();
        $cacheMock->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('Source:sourceCache'))
            ->willReturn(null);
        $cacheMock->expects($this->at(1))
            ->method('set')
            ->with(
                $this->equalTo('Source:sourceCache'),
                $this->equalTo(['id'=>1, 'name'=>'n1']),
                $this->equalTo(10000)
            );
        /** @noinspection PhpParamsInspection */
        $this->comxContext->setCache($cacheMock);
        $source = new Source($conf);
        $data = $source->loadData($this->comxContext, []);
        $this->assertEquals(['id'=>1, 'name'=>'n1'], $data);
    }

    public function testGetData_WithCache_Got()
    {
        $conf = Config::fromArray([
            'uri' => '/userService.php?id={request.url.query.b}',
            'firstEntryOnly' => true,
            'cache' => [
                'ttl' => 3000,
            ]
        ]);

        $cacheMock = $this->getMockBuilder(Cache::class)
            ->setMethods(['set', 'get'])
            ->getMock();
        $cacheMock->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('Source:/userService.php?id=1'))
            ->willReturn(['id'=>22, 'bname'=>'x']);

        /** @noinspection PhpParamsInspection */
        $this->comxContext->setCache($cacheMock);
        $source = new Source($conf);
        $data = $source->loadData($this->comxContext, []);
        $this->assertEquals(['id'=>22, 'bname'=>'x'], $data);
    }

}
