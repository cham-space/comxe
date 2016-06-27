<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/13
 * Time: 下午9:45
 */

namespace Gomeplus\Comx\Rest;


class ArrayAccessBaseTest extends \PHPUnit_Framework_TestCase {
    public function testBitwise() {
        $this->assertEquals(__SampleOfArrayAccess::COULD_GET & __SampleOfArrayAccess::ACCESSIBLE, __SampleOfArrayAccess::COULD_GET);
        $this->assertEquals(__SampleOfArrayAccess::COULD_GET | __SampleOfArrayAccess::ACCESSIBLE, __SampleOfArrayAccess::ACCESSIBLE);
        $this->assertEquals(__SampleOfArrayAccess::COULD_GET & __SampleOfArrayAccess::COULD_UNSET, __SampleOfArrayAccess::UNACCESSIBLE);
        $this->assertEquals(__SampleOfArrayAccess::COULD_GET | __SampleOfArrayAccess::COULD_UNSET | __SampleOfArrayAccess::COULD_SET, __SampleOfArrayAccess::ACCESSIBLE);
        $this->assertEquals(__SampleOfArrayAccess::COULD_GET | __SampleOfArrayAccess::WRITABLE, __SampleOfArrayAccess::ACCESSIBLE);
        $this->assertEquals(__SampleOfArrayAccess::COULD_SET | __SampleOfArrayAccess::COULD_UNSET, __SampleOfArrayAccess::WRITABLE);
    }

    /**
     * @var __SampleOfArrayAccess
     */
    protected $sample;
    protected function setUp()
    {
        parent::setUp();
        $this->sample = new __SampleOfArrayAccess();
    }

    /**
     * @dataProvider dataProviderOfTestOffsetGet
     */
    public function testOffsetGet($offset, $expectedValue)
    {
        $this->assertEquals($expectedValue, $this->sample[$offset]);
    }

    public function dataProviderOfTestOffsetGet()
    {
        return [
            ['aa', 1],
            ['a', 'a'],
            ['A', null],
            ['bb', null],
            ['cc', null],
            ['dd', "dd"],
            ['x', null],
        ];
    }

    /**
     * @dataProvider dataProviderOfTestOffsetSet
     */
    public function testOffsetSet($offset, $value, $expectedValue)
    {
        $this->sample[$offset] = $value;
        $this->assertEquals($expectedValue, $this->sample->$offset);
    }

    public function dataProviderOfTestOffsetSet()
    {
        return [
            ['aa', 2, 1],
            ['a', 1, null],
            ['bb', 2, 2],
            ['cc', 3, null],
            ['dd', 4, 4],
            ['z', 'x', null],
        ];
    }

    /**
     * @dataProvider dataProviderOfTestOffsetUnset
     */
    public function testOffsetUnset($offset, $isNull)
    {
        unset($this->sample[$offset]);
        if ($isNull) {
            $this->assertNull($this->sample->$offset);
        } else {
            $this->assertNotNull($this->sample->$offset);
        }
    }

    public function dataProviderOfTestOffsetUnset()
    {
        return [
            ['aa', false],
            ['ubb', false],
            ['ucc', true],
        ];
    }

    public function testArrayAccessOnAccessibleField() {
        $this->assertTrue(isset($this->sample['zz']));
        $this->assertNull($this->sample['zz']);
        $this->sample['zz'] = 'x1';
        $this->assertEquals('x1', $this->sample['zz']);
        unset($this->sample['zz']);
        $this->assertTrue(isset($this->sample['zz']));
        $this->assertNull($this->sample['zz']);
    }

    public function testExists()
    {

        $this->assertTrue($this->sample->offsetExists('a'));
        $this->assertTrue($this->sample->offsetExists('ucc'));
        $this->assertTrue(isset($this->sample['a']));
        $this->assertTrue(isset($this->sample['ucc']));

        $this->assertFalse($this->sample->offsetExists('xxx'));
        $this->assertFalse(isset($this->sample['xxx']));

    }


}

class __SampleOfArrayAccess extends ArrayAccessBase
{
    public $aa = 1,
        $a,
        $bb,
        $cc,
        $ucc = 'ucc',
        $zz,
        $dd,
        $z,
        $ubb = 'ubb';

    protected function getArrayAccessibleFields()
    {
        return [
            'aa'=>self::COULD_GET,
            'a'=>self::COULD_GET,
            'ubb'=> self::COULD_SET,
            'bb'=>self::COULD_SET,
            'cc'=>self::COULD_UNSET,
            'ucc'=>self::COULD_UNSET,
            'dd'=>self::ACCESSIBLE,
            'zz' =>self::ACCESSIBLE,
        ];
    }

    public function getA() {
        return 'a';
    }

    public function unsetUcc() {
        $this->ucc = null;
    }

    public function getAa()
    {
        return $this->aa;
    }

    public function setBb($value)
    {
        $this->bb = $value;
    }

    public function setDd($value)
    {
        $this->dd = $value;
    }

    public function getDd()
    {
        return 'dd';
    }

    public function getZz()
    {
        return $this->zz;
    }

    public function setZz($v)
    {
        $this->zz = $v;
    }

    public function unsetZz()
    {
        $this->zz = null;
    }
}