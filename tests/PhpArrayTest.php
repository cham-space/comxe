<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/6/23
 * Time: 下午6:52
 */

namespace gomeplus\comx;


class PhpArrayTest extends \PHPUnit_Framework_TestCase
{
    public function testRefEmbedToArray() {
        $o = ["a"=>1];
        $o1 = &$o;
        $arr = [&$o1];
        $arr[0]["a"] = 2;
        $this->assertEquals(2, $o['a']);
    }

    public function testRefAsRefEmbedToArray() {
        $o = ["a"=>1];
        $o1 = &$o;
        $arr = [$o1];
        $arr[0]["a"] = 2;
        $this->assertEquals(1, $o['a']);
    }
}
