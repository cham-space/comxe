<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/6/23
 * Time: 下午8:03
 */

namespace Gomeplus\Comx\Schema\Source;

use Comos\Config\Config;
use Gomeplus\Comx\Context\SourceBase\RedisSourceBase;
use Gomeplus\Comx\DecorTestBase;

class SourceWithBackupTest extends DecorTestBase
{
    public function testGet() {
        $confData = [
            'uri'=>'/userService.php?id=-1',
            'backup' => [
                'uri' => '/userService.php?id=8',
            ]
        ];
        
        $source = new Source(Config::fromArray($confData));
        $data = $source->loadData($this->comxContext, ['data'=>[],]);
        $this->assertEquals(['id'=>8, 'name'=>'n8'], $data);
    }

    public function testGet_FailToGetFromRedisButSucceedOnBackup()
    {
        $mockBaseId = 'mock';
        $redisSourceBaseMock = $this->getMockBuilder(RedisSourceBase::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'executeLoading'])
            ->getMock();
        $redisSourceBaseMock->expects($this->any())->method('getId')->will($this->returnValue($mockBaseId));
        $redisSourceBaseMock->expects($this->exactly(1))->method('executeLoading')
            ->with(($this->equalTo($this->comxContext)))
            ->willReturn(null);
        $this->comxContext->getSourceBaseFactory()->putSourceBase($redisSourceBaseMock);

        $confData = [
            'uri'=>'user:8',
            'base'=>'mock',
            'backup' => [
                'uri' => '/userService.php?id=7',
            ]
        ];
        $source = new Source(Config::fromArray($confData));
        $data = $source->loadData($this->comxContext, ['data'=>[],]);
        $this->assertEquals(['id'=>7, 'name'=>'n7'], $data);
    }

    public function testGet_WithBothBackupAndOnErrorByDefault() {
        $this->markTestIncomplete();
    }

    public function testGet_WithBothBackupAndOnErrorFail() {
        $this->markTestIncomplete();
    }
}