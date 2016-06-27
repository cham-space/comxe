<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/17
 * Time: 上午1:02
 */

namespace Gomeplus\Comx;


use Gomeplus\Comx\Schema\CannotFindSchemaException;
use Gomeplus\Comx\Schema\DataDecor\CompositionDecor;
use Gomeplus\Comx\Schema\Loader;

class HandlerTest extends DecorTestBase
{
    /**
     * @expectedException \Gomeplus\Comx\Rest\HttpResponseException
     * @expectedExceptionCode 403
     */
    public function testHandle_CannotFindSchema()
    {
        $handler = new Handler();
        $schemaLoader = $this->getMockBuilder(Loader::class)
            ->disableOriginalConstructor()
            ->setMethods(['load'])
            ->getMock();
        $schemaLoader->expects($this->any())
            ->method('load')
            ->willThrowException(new CannotFindSchemaException());
        /** @noinspection PhpParamsInspection */
        $this->comxContext->setSchemaLoader($schemaLoader);
        $handler->handle($this->comxContext);
    }

    /**
     * @expectedException \Gomeplus\Comx\Rest\HttpResponseException
     * @expectedExceptionCode 500
     */
    public function testHandle_Exception()
    {
        $handler = new Handler();
        $decorMock = $this->getMockBuilder(CompositionDecor::class)
            ->disableOriginalConstructor()
            ->setMethods(['decorate'])
            ->getMock();
        $decorMock->expects($this->at(0))
            ->method('decorate')
            ->willThrowException(new \Exception());
        $schemaLoader = $this->getMockBuilder(Loader::class)
            ->disableOriginalConstructor()
            ->setMethods(['load'])
            ->getMock();
        $schemaLoader->expects($this->any())
            ->method('load')
            ->willReturn($decorMock);
        /** @noinspection PhpParamsInspection */
        $this->comxContext->setSchemaLoader($schemaLoader);
        $handler->handle($this->comxContext);
    }
}
