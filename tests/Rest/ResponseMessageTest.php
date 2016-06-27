<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/28
 * Time: 下午1:58
 */

namespace Gomeplus\Comx\Rest;

class ResponseMessageTest extends \PHPUnit_Framework_TestCase
{
    public function test__Construct()
    {
        $response = new ResponseMessage(['a'=>1], '');
        $this->assertEquals(200, $response->getCode());
    }

    public function testSettersAndGetters()
    {
        $response = new ResponseMessage();
        $this->assertEquals(['a'=>1], $response->setData(['a'=>1])->getData());
        $this->assertEquals(200, $response->getCode());
        $this->assertEquals(201, $response->setCode(201)->getCode());
        $this->assertEquals('xxx', $response->setDebugInfo('xxx')->getDebugInfo());
        $this->assertEquals('xxxx', $response->setMessage('xxxx')->getMessage());
    }

    public function testSend()
    {
        $response = new ResponseMessage();
        $response->setData(['a'=>1]);
        ob_start();
        $response->send();
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(json_encode(['message'=>'', 'data'=>['a'=>1]], JSON_UNESCAPED_UNICODE), $result);
    }

    public function testSend_WithDebug()
    {
        $response = new ResponseMessage();
        $response->setData(['a'=>1]);
        $response->setDebugInfo('xxx');
        ob_start();
        $response->send();
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(json_encode(['message'=>'', 'data'=>['a'=>1], 'debug'=>'xxx'], JSON_UNESCAPED_UNICODE), $result);

    }
}