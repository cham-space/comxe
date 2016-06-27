<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/15
 * Time: 下午1:05
 */

namespace Gomeplus\Comx;


class AssertionTest extends \PHPUnit_Framework_TestCase {
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage my message
     */
    public function testAssert()
    {
        assert(false, 'my message');
    }
}
 