<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/16
 * Time: 上午9:40
 */

namespace Gomeplus\Comx\Context\SourceBase;


use Comos\Config\Config;

class SourceBaseFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Gomeplus\Comx\Context\SourceBase\UndefinedSourceBaseException
     */
    public function testGetSourceBase_UndefinedSourceBaseException()
    {
        $sourceBaseFactory = new SourceBaseFactory();
        $sourceBaseFactory->putSourceBase(new HttpSourceBase(Config::fromArray([
            'id' => 'x',
            'urlPrefix' => 'http://a.com/a',
        ])));
        $sourceBaseFactory->getSourceBase('y');
    }
}
