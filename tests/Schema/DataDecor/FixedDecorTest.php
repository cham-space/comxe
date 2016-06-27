<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/16
 * Time: 下午7:19
 */

namespace Gomeplus\Comx\Schema\DataDecor;


use Comos\Config\Config;
use Gomeplus\Comx\DecorTestBase;

class FixedDecorTest extends DecorTestBase
{
    public function testDecorate()
    {
        $decor = new FixedDecor(Config::fromArray(['fixedData'=>['a'=>1]]));
        $this->assertEquals('Fixed', $decor->getType());
        $data = ['b'=>2];
        $decor->decorate($data, $this->comxContext);
        $this->assertEquals(['a'=>1, 'b'=>2], $data);
    }

    public function testDecorate_WithField()
    {
        $decor = new FixedDecor(Config::fromArray(['fixedData'=>['a'=>1], 'field'=>'foo']));
        $this->assertEquals('Fixed', $decor->getType());
        $data = ['b'=>2];
        $decor->decorate($data, $this->comxContext);
        $this->assertEquals(['foo'=>['a'=>1], 'b'=>2], $data);
    }
}