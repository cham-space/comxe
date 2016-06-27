<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/1
 * Time: 下午4:25
 */

namespace Gomeplus\Comx\Rest;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $url = 'http://' . COMX_TEST_HOST . ':' . COMX_TEST_PORT . '/restSample.php';
        $client = new Client();
        $responseMessage = $client->request($url);
        $this->assertEquals(['id' => 1, 'name' => 'x'], $responseMessage->getData());
    }

    public function testPost()
    {
        $url = 'http://' . COMX_TEST_HOST . ':' . COMX_TEST_PORT . '/echoService.php';
        $client = new Client();
        $json = '{"a":1}';
        $responseMessage = $client->request($url, 'post', $json, ['Content-Type'=>'application/json']);
        $this->assertEquals('application/json', $responseMessage->getData()['server']['HTTP_CONTENT_TYPE']);
        $this->assertEquals('POST', $responseMessage->getData()['server']['REQUEST_METHOD']);
        $this->assertEquals($json, $responseMessage->getData()['bodyStr']);
        $this->assertEquals(1, $responseMessage->getData()['body']['a']);
    }

    public function testPut()
    {
        $url = 'http://' . COMX_TEST_HOST . ':' . COMX_TEST_PORT . '/echoService.php';
        $client = new Client();
        $json = '{"a":2}';
        $responseMessage = $client->request($url, 'put', $json, ['Content-Type'=>'application/json']);
        $this->assertEquals('application/json', $responseMessage->getData()['server']['HTTP_CONTENT_TYPE']);
        $this->assertEquals('gomeplus-comx', $responseMessage->getData()['server']['HTTP_USER_AGENT']);
        $this->assertEquals('PUT', $responseMessage->getData()['server']['REQUEST_METHOD']);
        $this->assertEquals($json, $responseMessage->getData()['bodyStr']);
        $this->assertEquals(['a'=>2], $responseMessage->getData()['body']);
    }

    public function testDelete()
    {
        $url = 'http://' . COMX_TEST_HOST . ':' . COMX_TEST_PORT . '/echoService.php';
        $client = new Client();
        $responseMessage = $client->request($url, 'delete');
        $this->assertEquals('DELETE', $responseMessage->getData()['server']['REQUEST_METHOD']);
        $this->assertEquals(200, $responseMessage->getCode());
    }

    /**
     * @expectedException \Gomeplus\Comx\Rest\NoResponseException
     * @throws BizResponseException
     * @throws Exception
     * @throws InvalidMessageException
     * @throws MessageDecodingException
     */
    public function testRequest_BaaHost()
    {
        $url = 'http://' . COMX_TEST_HOST . ':' . (COMX_TEST_PORT+1) . '/echoService.php';
        $client = new Client();
        $client->request($url);
    }

    /**
     * @expectedException \Gomeplus\Comx\Rest\MessageDecodingException
     * @throws BizResponseException
     * @throws Exception
     * @throws InvalidMessageException
     * @throws MessageDecodingException
     */
    public function testRequest_BadJson()
    {
        $url = 'http://' . COMX_TEST_HOST . ':' . COMX_TEST_PORT . '/badService.php?type=badJson';
        $client = new Client();
        $client->request($url);
    }

    /**
     * @expectedException \Gomeplus\Comx\Rest\InvalidMessageException
     * @throws BizResponseException
     * @throws Exception
     * @throws InvalidMessageException
     * @throws MessageDecodingException
     */
    public function testRequest_InvalidMessage_WithoutMessage()
    {
        $url = 'http://' . COMX_TEST_HOST . ':' . COMX_TEST_PORT . '/badService.php?type=invalidMessage';
        $client = new Client();
        $client->request($url);
    }

    /**
     * @expectedException \Gomeplus\Comx\Rest\InvalidMessageException
     * @throws BizResponseException
     * @throws \Exception
     */
    public function testRequest_InvalidMessage_WithoutData()
    {
        $url = 'http://' . COMX_TEST_HOST . ':' . COMX_TEST_PORT . '/badService.php?type=invalidMessage1';
        $client = new Client();
        $client->request($url);
    }

    /**
     * @expectedException \Gomeplus\Comx\Rest\BizResponseException
     * @expectedExceptionMessage 资源不存在
     * @expectedExceptionCode 404
     * @throws \Exception
     */
    public function testRequest_404Code()
    {
        $url = 'http://' . COMX_TEST_HOST . ':' . COMX_TEST_PORT . '/badService.php?type=404';
        $client = new Client();
        $client->request($url);
    }
}