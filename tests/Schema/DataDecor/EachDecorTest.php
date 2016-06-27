<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/15
 * Time: 下午7:04
 */

namespace Schema\DataDecor;


use Comos\Config\Config;
use Gomeplus\Comx\DecorTestBase;
use Gomeplus\Comx\Schema\DataDecor\EachDecor;

class EachDecorTest extends DecorTestBase
{
    /**
     * @dataProvider dataProviderForTestDecorate
     */
    public function testDecorate($data, Config $conf, $expectedData)
    {
        $decor = new EachDecor($conf);
        $decor->decorate($data, $this->comxContext);
        $this->assertEquals($expectedData, $data);
    }

    public function dataProviderForTestDecorate()
    {
        return [
            [
                'inputData' => [
                    'items' => [
                        [
                            'id' => 11,
                            'title' => 'a',
                            'userId' => 1,
                        ],
                        [
                            'id' => 12,
                            'title' => 'b',
                            'userId' => 2,
                        ]
                    ]
                ],
                'conf' => Config::fromArray([
                   'type' => 'Each',
                    'refJsonPath' => '$.items.*',
                    'field' => 'user',
                    'source' => [
                        'uri' => '/userService.php?id={ref.userId}',
                    ]
                ]),
                'expectedData' => [
                    'items' => [
                        [
                            'id'=>11,
                            'title' => 'a',
                            'userId' => 1,
                            'user' => [
                                'id' => 1,
                                'name' => 'n1',
                            ],
                        ],
                        [
                            'id'=>12,
                            'title' => 'b',
                            'userId' => 2,
                            'user' => [
                                'id' => 2,
                                'name' => 'n2',
                            ],
                        ],
                    ]
                ]
            ],
            //Case 2
            [
                'inputData' => [
                    'users' => [
                        [
                            'id' => 11,
                        ],
                        [
                            'id' => 12,
                        ]
                    ]
                ],
                'conf' => Config::fromArray([
                    'type' => 'Each',
                    'refJsonPath' => '$.users.*',
                    'source' => [
                        'uri' => '/userService.php?id={ref.id}',
                    ]
                ]),
                'expectedData' => [
                    'users' => [
                        [
                            'id'=>11,
                            'name' => 'n11',
                        ],
                        [
                            'id'=>12,
                            'name' => 'n12',
                        ],
                    ]
                ]
            ]
        ];
    }
}
