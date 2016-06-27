<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/18
 * Time: 下午7:59
 */

namespace Gomeplus\Comx\Schema\Source;


use Comos\Config\Config;
use Gomeplus\Comx\DecorTestBase;
use Gomeplus\Comx\Schema\DataDecor\AbstractDecor;
use Gomeplus\Comx\Schema\DataDecor\DecorFactory;
use Gomeplus\Comx\Schema\Loader;

class SourceWithSelfBaseTest extends DecorTestBase
{
    public function testDecorate()
    {
        $loader = $this->getMockBuilder(Loader::class)
            ->setMethods(['load'])
            ->disableOriginalConstructor()
            ->getMock();
        $loader->expects($this->at(0))
            ->method('load')
            ->with($this->equalTo('/foo/bar'))
            ->willReturn(
                DecorFactory::create(Config::fromArray([
                    'decors'=>[
                        [
                            'type'=>'Fixed',
                            'fixedData'=>[
                                'a' => 1,
                            ]
                        ]
                    ]
                ]), AbstractDecor::TYPE_COMPOSITION)
            );
        /** @noinspection PhpParamsInspection */
        $this->comxContext->setSchemaLoader($loader);
        $conf = Config::fromArray([
            'uri' => '/foo/bar',
            'base' => 'self',
        ]);
        $source = new Source($conf);
        $data = $source->loadData($this->comxContext, []);
        $this->assertEquals(['a'=>1], $data);
    }
}