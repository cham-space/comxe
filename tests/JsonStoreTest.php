<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/24
 * Time: 下午4:28
 */

namespace Gomeplus\Comx;


use Peekmo\JsonPath\JsonStore;

class JsonStoreTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $data = [
            'a' => 'x',
            'b' => 'y',
            'c' => [
                ['id'=>1],
                ['id'=>2]
            ]
        ];
        $store = new JsonStore();
        $res = $store->get($data, 'c');

        $this->assertInternalType('array', $res);
        $this->assertEquals(1, count($res));
        $res[0][0]['id'] = 3;
        $this->assertEquals(3, $data['c'][0]['id']);
        
        $res = $store->get($data, '$.c.*');
        $this->assertEquals(2, count($res));

        $res = $store->get($data, '$.c[0]');
        $this->assertEquals([['id'=>3]], $res);

    }
}
