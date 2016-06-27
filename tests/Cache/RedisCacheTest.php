<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/16
 * Time: 下午1:49
 */

namespace Gomeplus\Comx\Cache;


use Comos\Config\Config;
use Predis\Client;

class RedisCacheTest extends \PHPUnit_Framework_TestCase
{
    public function testFromConf()
    {
        $conf = Config::fromArray([]);
        $rc = RedisCache::fromConf($conf);
        $this->assertInstanceOf(RedisCache::class, $rc);
    }

    public function testGet_NullResult()
    {
        $key = 'a:b:1';

        $predisClientMock = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', 'set'])
            ->getMock();
        $predisClientMock->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo($key))
            ->willReturn(null);

        $conf = Config::fromArray([]);
        $rc = RedisCache::fromConf($conf);
        /** @noinspection PhpParamsInspection */
        $rc->setRedisClient($predisClientMock);
        $this->assertNull($rc->get($key));
    }

    public function testGet()
    {
        $key = 'a:b:c';
        $predisClientMock = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', 'set'])
            ->getMock();
        $predisClientMock->expects($this->exactly(1))
            ->method('get')
            ->with($this->equalTo($key))
            ->willReturn(serialize(['a'=>1]));
        $conf = Config::fromArray([]);
        $rc = RedisCache::fromConf($conf);
        /** @noinspection PhpParamsInspection */
        $rc->setRedisClient($predisClientMock);

        $result = $rc->get($key);

        $this->assertEquals(['a'=>1], $result);
    }

    public function testSet()
    {
        $key = 'a:b:c';
        $predisClientMock = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', 'set'])
            ->getMock();
        $predisClientMock->expects($this->exactly(1))
            ->method('set')
            ->with(
                $this->equalTo($key),
                $this->equalTo(serialize(['a'=>2])),
                $this->equalTo('PX'),
                $this->equalTo(1000)
            );
        $conf = Config::fromArray([]);
        $rc = RedisCache::fromConf($conf);
        /** @noinspection PhpParamsInspection */
        $rc->setRedisClient($predisClientMock);

        $rc->set($key, ['a'=>2], 1000);
    }



    public function testSetAndGet_KeyIsTooLong()
    {
        $predisClientMock = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', 'set'])
            ->getMock();
        $predisClientMock->expects($this->never())
            ->method('get');
        $predisClientMock->expects($this->never())
            ->method('set');
        $conf = Config::fromArray([]);
        $rc = RedisCache::fromConf($conf);
        /** @noinspection PhpParamsInspection */
        $rc->setRedisClient($predisClientMock);

        $key = str_repeat('a', 256);
        $rc->set($key, ['a'=>1], 10000);
        $this->assertNull($rc->get($key));
    }
}