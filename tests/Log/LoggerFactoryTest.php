<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/28
 * Time: 上午12:30
 */

namespace Log;


use Gomeplus\Comx\Log\LoggerFactory;
use Gomeplus\Comx\Log\NullLogger;

class LoggerFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        LoggerFactory::setLogger(new NullLogger());

        parent::tearDown();
    }

    public function testGetLogger()
    {
        $logger = LoggerFactory::getLogger();
        $this->assertInstanceOf(NullLogger::class, $logger);

    }

    public function testSetLogger()
    {
        $logger = LoggerFactory::createLogger('/tmp', false);
        LoggerFactory::setLogger($logger);
        $this->assertTrue($logger ===  LoggerFactory::getLogger());

        $logger = LoggerFactory::createLogger('/tmp', true);
        LoggerFactory::setLogger($logger);
        $this->assertTrue($logger ===  LoggerFactory::getLogger());
    }

}
