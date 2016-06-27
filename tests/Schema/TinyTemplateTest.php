<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/20
 * Time: 下午1:34
 */

namespace Schema;


use Gomeplus\Comx\Schema\TinyTemplate;

class TinyTemplateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $tpl
     * @param $vars
     * @param $expectedResult
     * @dataProvider dataProviderForTestRender
     */
    public function testRender($tpl, $vars, $expectedResult)
    {
        $tt = new TinyTemplate($tpl);
        $result = $tt->render($vars);
        $this->assertEquals($expectedResult, $result);
    }

    public function dataProviderForTestRender()
    {
        return [
            ['111{a.b.c}222', ['a'=>['b'=>['c'=>'x']]], '111x222'],
            ['111{a.b.c}22{a.b.c}2', ['a'=>['b'=>['c'=>'x']]], '111x22x2'],
            ['111{a.b.c}22{z}2', ['a'=>['b'=>['c'=>'x']], 'z'=>'X'], '111x22X2'],
            ['111{a.b.c}2{unknown}2{z}2', ['a'=>['b'=>['c'=>'x']], 'z'=>'X'], '111x22X2'],
            ['111{a.b.c}2{}2{z}2', ['a'=>['b'=>['c'=>'x']], 'z'=>'X'], '111x22X2'],
        ];
    }

}
