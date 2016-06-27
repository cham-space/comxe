<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/8
 * Time: 下午8:33
 */

namespace Gomeplus\Comx\Schema\DataDecor;

use Comos\Config\Config;
use Gomeplus\Comx\DecorTestBase;

/**
 * 本测试用例不再用于测试DefaultDecor(已废弃)
 * 仅测试 EachDecor和原DefaultDecor的兼容性
 * Class DefaultDecorTest
 * @package Gomeplus\Comx\Schema\DataDecor
 */
class DefaultDecorTest extends DecorTestBase
{


    /**
     * @param $conf
     * @param $expectedData
     * @dataProvider dataProviderForTestDecorate
     */
    public function testDecorate($conf, $expectedData)
    {
        $decor = new EachDecor($conf);
        $data = ['a' => 1];
        $decor->decorate($data, $this->comxContext);
        $this->assertEquals($expectedData, $data);
    }

    public function dataProviderForTestDecorate()
    {
        return [
            //without field
            [
                Config::fromArray([
                    'source' => [
                        'uri' => '/restSample.php'
                    ],
                ]),
                ['a'=>1, 'id'=>1, 'name'=>'x']
            ],
            //with field
            [
                Config::fromArray([
                    'source' => [
                        'uri' => '/restSample.php'
                    ],
                    'field' => 'sample'
                ]),
                ['a' => 1, 'sample' => ['id' => 1, 'name' => 'x']]
            ],
            //field exists
            [
                Config::fromArray([
                    'source' => [
                        'uri' => '/restSample.php'
                    ],
                    'field' => 'a'
                ]),
                ['a' => ['id' => 1, 'name' => 'x']]
            ],
            //jsonPath with firstEntryOnly
            [
                Config::fromArray([
                    'source' => [
                        'uri' => '/restSample.php',
                        'jsonPath' => '$.name',
                        'firstEntryOnly' => true,
                    ],
                    'field' => 'name',
                ]),
                ['a'=>1, 'name'=>'x'],
            ],
            //with children
            [
                Config::fromArray([
                    'source' => [
                        'uri' => '/restSample.php',
                    ],
                    'decors' => [
                        [
                            'source' => ['uri'=> '/restSample.php'],
                            'field' => 'c1',
                        ],
                        [
                            'source' => ['uri'=> '/restSample.php'],
                            'field' => 'c2',
                        ]
                    ]
                ]),
                [
                    'a' => 1, 'id'=>1, 'name' => 'x',
                        'c1'=>['id'=>1,'name'=>'x'],
                        'c2'=>['id'=>1,'name'=>'x']
                ]
            ],
            //with OnError ignore
            [
                Config::fromArray([
                    'source' => [
                        'uri' => '/restSample.php',
                    ],
                    'decors' => [
                        [
                            'type'=>'Each',
                            'source' => ['uri'=> '/badService.php'],
                            'field' => 'c1',
                            'onError' => ['type'=>'ignore']
                        ],
                        [
                            'source' => ['uri'=> '/restSample.php'],
                            'field' => 'c2',
                        ]
                    ]
                ]),
                [
                    'a' => 1, 'id'=>1, 'name' => 'x',
                    'c2'=>['id'=>1,'name'=>'x']
                ]
            ],
            //with OnError ignore
            [
                Config::fromArray([
                    'source' => [
                        'field' => 'x',
                        'uri' => '/badService.php',
                        'onError' => ['type'=>'ignore']
                    ],
                    'decors' => [
                        [
                            'type'=>'Each',
                            'source' => ['uri'=> '/restSample.php'],
                            'field' => 'c1',
                        ],
                        [
                            'source' => ['uri'=> '/restSample.php'],
                            'field' => 'c2',
                        ]
                    ]
                ]),
                [
                    'a'=>1,
                    'c1'=>['id'=>1,'name'=>'x'],
                    'c2'=>['id'=>1,'name'=>'x'],
                ]
            ]

        ];

    }


}
