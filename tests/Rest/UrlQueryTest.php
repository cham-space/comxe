<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/14
 * Time: 下午2:13
 */

namespace Gomeplus\Comx\Rest;


class UrlQueryTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var UrlQuery
     */
    protected $q0, $q1, $q2, $q3, $q4, $q5;

    protected function setUp()
    {
        parent::setUp();
        $this->q0 = new UrlQuery('');
        $this->q1 = new UrlQuery('a=1');
        $this->q2 = new UrlQuery('a=1&b=2');
        $this->q3 = new UrlQuery('a=1&b=2&c=3');
        $this->q4 = new UrlQuery('a[]=1&a[]=2&a[]=3');
        $this->q5 = new UrlQuery('a=a+b&b=2&c=3');
    }

    public function test__ToString()
    {
        $this->assertEquals('', strval($this->q0));
        $this->assertEquals('a=1', strval($this->q1));
    }

    public function testGetParameters()
    {
        $this->assertEquals([], $this->q0->getParameters());
        $this->assertEquals(['a'=>'1'], $this->q1->getParameters());
        $this->assertEquals(['a'=>'1', 'b'=>'2'], $this->q2->getParameters());
        $this->assertEquals(['a'=>'1', 'b'=>'2'], $this->q2->getParameters());
        $this->assertEquals(['a'=>'a b', 'b'=>'2', 'c'=>'3'], $this->q5->getParameters());
    }

    public function testGetParameter()
    {
        $this->assertEquals('1', $this->q1->getParameter('a'));
        $this->assertNull($this->q1->getParameter('b'));
        $this->assertEquals('2', $this->q2->getParameter('b'));
        $this->assertEquals(['1', '2', '3'], $this->q4->getParameter('a'));
        $this->assertNull($this->q0->getParameter('x'));
    }

    public function testArrayAccess()
    {
        $this->assertEquals('1', $this->q1['a']);
        $this->assertEquals('3', $this->q4['a']['2']);
        $this->assertTrue(isset($this->q1['a']));
        $this->q1['a'] = '2';
        $this->assertEquals('1', $this->q1['a']);

        unset($this->q1['a']);
        $this->assertEquals('1', $this->q1['a']);
    }
}