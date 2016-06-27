<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/3
 * Time: 上午9:05
 */

namespace Gomeplus\Comx\Schema\DataDecor;

use Comos\Config\Config;

class DecorFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $defaultDecor = DecorFactory::create(Config::fromArray(['source'=>['uri'=>'/a/b']]));
        $this->assertInstanceOf(EachDecor::class, $defaultDecor);
    }

    /**
     * @dataProvider dataProviderForTestCreate_ByDataProvider
     */
    public function testCreate_ByDataProvider($conf, $clazz)
    {
        $theDecor = DecorFactory::create(Config::fromArray($conf));
        $this->assertInstanceOf($clazz, $theDecor);
    }

    public function dataProviderForTestCreate_ByDataProvider() {
        return [
            [['type'=>'Each', 'refJsonPath'=>'$', 'source'=>['uri'=>'/a/b']], EachDecor::class],
            [['type'=>'Composition', 'decors'=>[]], CompositionDecor::class]

        ];
    }

    /**
     * @expectedException \Gomeplus\Comx\Schema\DataDecor\UnknownDecorTypeException
     */
    public function testCreate_UnknownType()
    {
        DecorFactory::create(Config::fromArray(['type'=>'UFO','source'=>['uri'=>'/a/b']]));
    }

    public function testCreate_ForceType()
    {
        $decor = DecorFactory::create(Config::fromArray(['type'=>'UFO','source'=>['uri'=>'/a/b']]), AbstractDecor::TYPE_COMPOSITION);
        $this->assertInstanceOf(CompositionDecor::class, $decor);
    }

}
