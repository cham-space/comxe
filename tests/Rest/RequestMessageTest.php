<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/28
 * Time: 上午10:00
 */

namespace Gomeplus\Comx\Rest;

class RequestMessageTest extends \PHPUnit_Framework_TestCase
{
    public function test__Construct()
    {
        $req = new RequestMessage('http://a.com/x');
        $this->assertEquals('http://a.com/x', strval($req->getUrl()));
    }

    public function testGetHeadersFromEnv()
    {
        $vars = [
            'HTTP_USER_AGENT' => 'Z-Agent',
            'HTTP_CONTENT_TYPE' => 'application/json',
            'REQUEST_METHOD' => 'GET',
        ];

        $headers = RequestMessage::getHeadersFromEnv($vars);
        $this->assertEquals(
            [
                'User-Agent'=>'Z-Agent',
                'Content-Type'=>'application/json',
            ],
            $headers

        );
    }

}
