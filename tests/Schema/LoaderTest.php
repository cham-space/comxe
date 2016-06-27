<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/19
 * Time: 下午3:18
 */

namespace Gomeplus\Comx\Schema;


use Gomeplus\Comx\Schema\DataDecor\CompositionDecor;

class LoaderTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var Loader
     */
    protected $loader;

    protected function setUp()
    {
        chmod(COMX_TEST_ROOT . '/_testHome/apis/ext/social/cannotRead/get.json', 0077);

        $this->loader = new Loader(dirname(__DIR__) . '/_testHome');
        parent::setUp();
    }

    protected function tearDown()
    {
        parent::tearDown();
        chmod(COMX_TEST_ROOT . '/_testHome/apis/ext/social/cannotRead/get.json', 0777);
        $this->loader = null;
    }

    public function testLoad()
    {
        $conf = $this->loader->load('/ext/social/topic');
        $this->assertInstanceOf(CompositionDecor::class, $conf);
    }

    /**
     * @expectedException \Gomeplus\Comx\Schema\CannotFindSchemaException
     */
    public function testLoad_CannotFindApiConf()
    {
        $this->loader->load('/ext/social/topicDoesNotExist');
    }

    /**
     * @expectedException \Gomeplus\Comx\Schema\Exception
     * @expectedExceptionMessage invalid conf path:
     */
    public function testLoad_IllegalPath()
    {
        $this->loader->load('/ext/../../../_testHome1');
    }

    /**
     * @expectedException \Comos\Config\Exception
     */
    public function testLoad_BadFormat()
    {
        $this->loader->load('/ext/social/bad');
    }

    /**
     * @expectedException \Comos\Config\Exception
     * @expectedExceptionMessage cannot read conf file.
     */
    public function testLoad_CannotRead()
    {
        $this->loader->load('/ext/social/cannotRead');
    }
}
 