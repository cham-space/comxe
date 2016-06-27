<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/29
 * Time: 下午5:07
 */

namespace Gomeplus\Comx\Schema\ScriptLoader;


class ScriptLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad() {
        $loader = new ScriptLoader(COMX_TEST_ROOT.'/_testHome');
        $result = $loader->load('x');
        $this->assertTrue(is_callable($result));
    }

    /**
     * @expectedException \Gomeplus\Comx\Schema\ScriptLoader\CannotFindScriptException
     */
    public function testLoad_CannotFindFile()
    {
        $loader = new ScriptLoader(COMX_TEST_ROOT.'/_testHome');
        $loader->load('y');
    }

    /**
     * @expectedException \Gomeplus\Comx\Schema\ScriptLoader\InvalidScriptException
     */
    public function testLoad_InvalidScript()
    {
        $loader = new ScriptLoader(COMX_TEST_ROOT.'/_testHome');
        $loader->load('w');
    }
    /**
     * @expectedException \Gomeplus\Comx\Schema\SecurityException
     */
    public function testLoad_SecurityException()
    {
        $loader = new ScriptLoader(COMX_TEST_ROOT.'/_testHome');
        $loader->load('../../_testHome1/scripts/y');
    }

}
