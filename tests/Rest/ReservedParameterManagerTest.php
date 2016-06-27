<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/29
 * Time: 上午1:36
 */

namespace Gomeplus\Comx\Rest;

class ReservedParameterManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReservedParameterManager
     */
    protected $manager;
    protected function setUp()
    {
        parent::setUp();
        $this->manager = new ReservedParameterManager(
            ['userId'=>'321', 'loginToken'=>'x123'], ['traceId'=>'ttt', 'loginToken'=>'x000', 'accept' => '*/*']
        );
    }

    public function testGetReservedQueryParams()
    {
        $this->assertEquals(['userId'=>'321', 'loginToken'=>'x123'], $this->manager->getReservedQueryParams());
    }

    public function testGetFilteredReservedHeaders()
    {
        $this->assertEquals(['X-Gomeplus-Trace-Id'=>'ttt', 'Accept'=>'*/*'], $this->manager->getFilteredReservedHeaders());
    }

    public function testFromRequest()
    {
        $req = new RequestMessage('http://a.com/?a=1&userId=323&traceId=221&accept=application/json',
            'get', null,
            [
                'X-Gomeplus-Login-Token' => '3308',
                'X-Gomeplus-Device' => 'a/b/c',
            ]);
        $m = ReservedParameterManager::fromRequest($req);
        $this->assertEquals(['userId'=>'323', 'traceId'=>'221', 'accept'=>'application/json'], $m->getReservedQueryParams());
        $this->assertEquals(['X-Gomeplus-Login-Token'=>'3308', 'X-Gomeplus-Device'=>'a/b/c'], $m->getFilteredReservedHeaders());
    }
}
