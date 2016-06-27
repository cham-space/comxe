<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/11
 * Time: 下午10:54
 */

namespace Gomeplus\Comx\Schema\DataDecor;


use Comos\Config\Config;
use Gomeplus\Comx\Cache\Cache;
use Gomeplus\Comx\DecorTestBase;


/**
 * 本测试用例不再用于测试DefaultDecor(已废弃)
 * 仅测试 EachDecor和原DefaultDecor的兼容性
 * Class DefaultDecorWithCacheTest
 * @package Gomeplus\Comx\Schema\DataDecor
 */
class DefaultDecorWithCacheTest extends DecorTestBase
{

    /**
     * @param $confData
     * @param $cachedData
     * @param $expectedResult
     * @dataProvider dataProviderForTestDecorate_CacheWithoutChildren
     */
    public function testDecorateWithoutChildren($confData, $cachedData, $expectedResult, $cacheKey)
    {
        $conf = Config::fromArray($confData);

        $cacheMock = $this->getMockBuilder(Cache::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', 'set'])
            ->getMock();

        $cacheMock->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo($cacheKey))
            ->willReturn(null);


        $cacheMock->expects($this->at(1))
            ->method('set')
            ->with($this->equalTo($cacheKey), $this->equalTo($cachedData), $this->equalTo(500));

        $cacheMock->expects($this->at(2))
            ->method('get')
            ->with($this->equalTo($cacheKey))
            ->willReturn($cachedData);

        /** @noinspection PhpParamsInspection */
        $this->comxContext->setCache($cacheMock);

        $decor = new EachDecor($conf);
        $data = [];
        $decor->decorate($data, $this->comxContext);

        $this->assertEquals($expectedResult, $data);

        $data = [];
        $decor = new EachDecor($conf);
        $decor->decorate($data, $this->comxContext);
        $this->assertEquals($expectedResult, $data);
    }

    public function dataProviderForTestDecorate_CacheWithoutChildren()
    {
        $list[] = [
            'confData'=>[
                'source' => [
                    'uri' => '/plusService.php?origin={request.url.query.b}'
                ],
                'cache' => [
                    'key' => 'dc',
                    'ttl' => 500,
                ]
            ],
            'cachedData'=>['result' => 2],
            'expectedResult'=>['result' => 2],
            'cacheKey' => 'Decor:/a:dc',
        ];
        
        $list[] = [
            'confData'=>[
                'source' => [
                    'uri' => '/plusService.php?origin={request.url.query.b}'
                ],
                'cache' => [
                    'key' => 'dc',
                    'ttl' => 500,
                    'withChildren' => false,
                ],
                'decors'=>[
                    [
                        'field' => 'c',
                        'source'=> [
                            'uri'=>'/plusService.php?origin={request.url.query.b}'
                        ],
                    ]
                ]
            ],
            'cachedData'=>['result' => 2],
            'expectedResult'=>['result' => 2, 'c' => ['result' => 2]],
            'cacheKey' => 'Decor:/a:dc',
        ];

        $list[] = [
            'confData'=>[
                'source' => [
                    'uri' => '/plusService.php?origin={request.url.query.b}'
                ],
                'cache' => [
                    'key' => 'dc',
                    'ttl' => 500,
                ],
                'decors'=>[
                    [
                        'field' => 'c',
                        'source'=> [
                            'uri'=>'/plusService.php?origin={request.url.query.b}'
                        ],
                    ]
                ]
            ],
            'cachedData'=>['result' => 2],
            'expectedResult'=>['result' => 2, 'c' => ['result' => 2]],
            'cacheKey' => 'Decor:/a:dc',
        ];

        $list[] = [
            'confData'=>[
                'source' => [
                    'uri' => '/plusService.php?origin={request.url.query.b}'
                ],
                'cache' => [
                    'key' => 'dc',
                    'ttl' => 500,
                    'isGlobal' => true,
                ],
                'decors'=>[
                    [
                        'field' => 'c',
                        'source'=> [
                            'uri'=>'/plusService.php?origin={request.url.query.b}'
                        ],
                    ]
                ]
            ],
            'cachedData'=>['result' => 2],
            'expectedResult'=>['result' => 2, 'c' => ['result' => 2]],
            'cacheKey' => 'Decor::dc',
        ];

        return $list;
    }

    public function testDecorate_CacheWithChildren()
    {
        $result = ['result' => 2, 'c' => ['result' => 2]];

        $cacheMock = $this->getMockBuilder(Cache::class)
            ->disableOriginalConstructor()
            ->setMethods(['get','set'])
            ->getMock();

        $cacheMock->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('Decor:/a:dc'))
            ->willReturn(null);

        $cacheMock->expects($this->at(1))
            ->method('set')
            ->with($this->equalTo('Decor:/a:dc'), $this->equalTo($result), $this->equalTo(500));

        $cacheMock->expects($this->at(2))
            ->method('get')
            ->with($this->equalTo('Decor:/a:dc'))
            ->willReturn($result);

        /** @noinspection PhpParamsInspection */
        $this->comxContext->setCache($cacheMock);
        $conf = Config::fromArray([
            'source' => [
                'uri' => '/plusService.php?origin={request.url.query.b}'
            ],
            'cache' => [
                'key' => 'dc',
                'ttl' => 500,
                'withChildren' => true,
            ],
            'decors'=>[
                [
                    'field' => 'c',
                    'source'=> [
                        'uri'=>'/plusService.php?origin={request.url.query.b}'
                    ],
                ]
            ]
        ]);
        $decor = new EachDecor($conf);
        $data = [];
        $decor->decorate($data, $this->comxContext);
        $this->assertEquals($result, $data);

        $data = [];
        $decor = new EachDecor($conf);
        $decor->decorate($data, $this->comxContext);
        $this->assertEquals($result, $data);
    }
}