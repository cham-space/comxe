<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/13
 * Time: 下午6:06
 */

namespace Gomeplus\Comx\Schema\DataDecor;


use Comos\Config\Config;
use Gomeplus\Comx\Context\Context;
use Gomeplus\Comx\Schema\OnError\UnsupportedStrategyException;
use Gomeplus\Comx\Schema\ScriptLoader\ScriptLoader;

class ScriptDecorTest extends \PHPUnit_Framework_TestCase
{
    public function testDecorate()
    {
        $context = new Context();
        $scriptLoaderMock = $this->getMockBuilder(ScriptLoader::class)
            ->setMethods(['load'])
            ->disableOriginalConstructor()
            ->getMock();
        $scriptLoaderMock->expects($this->exactly(1))
            ->method('load')
            ->with($this->equalTo('foo'))
            ->willReturn(
                function(&$data){
                    $data = ['a'=>1];
                }
            );

        /** @noinspection PhpParamsInspection */
        $context->setScriptLoader($scriptLoaderMock);
        $decor = new ScriptDecor(Config::fromArray([
            'type' => 'Script',
            'script' => 'foo'
        ]));

        $data = [];
        $decor->decorate($data, $context);
        $this->assertEquals(['a'=>1], $data);
    }

    public function testDecorate_WithIllegalOnError()
    {
        $context = new Context();
        $scriptLoaderMock = $this->getMockBuilder(ScriptLoader::class)
            ->setMethods(['load'])
            ->disableOriginalConstructor()
            ->getMock();
        $scriptLoaderMock->expects($this->exactly(1))
            ->method('load')
            ->with($this->equalTo('foo'))
            ->willReturn(
                function(&$data){
                    is_null($data);
                    throw new \Exception();
                }
            );

        /** @noinspection PhpParamsInspection */
        $context->setScriptLoader($scriptLoaderMock);
        $decor = new ScriptDecor(Config::fromArray([
            'type' => 'Script',
            'script' => 'foo',
            'onError' => [
                'type' => 'byDefault',
                'defaultValue' => ['a' => 1],
            ]
        ]));
        $data = ['y' => 2];
        try {
            $decor->decorate($data, $context);
            $this->fail('expectsException');
        } catch (UnsupportedStrategyException $ex) {
            $this->assertEquals(['y'=>2], $data);
        }

    }

}
