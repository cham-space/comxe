<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/16
 * Time: 上午9:48
 */

namespace Gomeplus\Comx\Debug;


class DebuggerTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        Debugger::disable();
        parent::tearDown();
    }

    public function testMethods()
    {
        $this->assertFalse(Debugger::isEnabled());
        Debugger::enable();
        $this->assertTrue(Debugger::isEnabled());
        $h0 = new \stdClass();
        $h1 = new \stdClass();
        $h0->a = $h1;
        $h1->a = $h0;

        Debugger::appendDebugInfo('cycle', ['obj'=>$h0]);
        Debugger::appendDebugInfo('msg', ['data'=>1]);
        $this->assertRegExp('/STEP\\(1\\)/', Debugger::getLogString());
        $this->assertRegExp('/STEP\\(2\\)\\tMSG\\(msg\\)/', Debugger::getLogString());
    }
}
