<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/16
 * Time: 下午7:07
 */

namespace Gomeplus\Comx\Schema\DataDecor;


use Comos\Config\Config;
use Gomeplus\Comx\DecorTestBase;

class BatchDecorTest extends DecorTestBase
{
    public function testGetType()
    {
        $decor = new BatchDecor(Config::fromArray([]));
        $this->assertEquals('Batch', $decor->getType());
    }

    public function testDecorate()
    {
        $decor = new BatchDecor(Config::fromArray([
            'refJsonPath' => '$.items.*',
            'field' => 'user',
            'mapping' => [
                'foreignId' => 'userId',
                'refId' => 'id',
            ],
            'source' => [
                'uri' => '/usersService.php?ids={foreignIds}',
                'jsonPath' => '$.users.*',
            ],
        ]));
        $data = [
            'items' => [
                [
                    'id'=>1,
                    'userId' => 11,
                ],
                [
                    'id'=>2,
                    'userId' => 22,
                ]
            ]
        ];
        $decor->decorate($data, $this->comxContext);

        $this->assertEquals([
            'items' => [
                [
                    'id' => 1,
                    'userId' => 11,
                    'user' => [
                        'id' => 11,
                        'name' => 'n11',
                    ]
                ],
                [
                    'id' => 2,
                    'userId' => 22,
                    'user' => [
                        'id' => 22,
                        'name' => 'n22',
                    ]
                ]
            ]
        ], $data);
    }
}