<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/16
 * Time: 下午10:42
 */

namespace Gomeplus\Comx\Schema\OnError;


use Comos\Config\Config;

class StrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Gomeplus\Comx\Schema\OnError\UnknownOnErrorStrategyException
     * @expectedExceptionMessage foo
     */
    public function testFromConf()
    {
        Strategy::fromConf(Config::fromArray([
            'type' => 'foo',
        ]));
    }

}
