<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/30
 * Time: 下午1:26
 */

namespace Gomeplus\Comx\Rest;

class HttpCodesTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMessage()
    {
        $this->assertEquals('Not Found', HttpCodes::getMessage(404));
        $this->assertEquals('OK', HttpCodes::getMessage(200));
        $this->assertEquals('Created', HttpCodes::getMessage(201));
        $this->assertEquals('Bad Request', HttpCodes::getMessage(400));
        $this->assertEquals('Internal Server Error', HttpCodes::getMessage(500));
        $this->assertNull(HttpCodes::getMessage(601));
    }
}
