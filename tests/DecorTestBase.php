<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/15
 * Time: 下午7:09
 */

namespace Gomeplus\Comx;


use Comos\Config\Config;
use Gomeplus\Comx\Context\Context;
use Gomeplus\Comx\Context\SourceBase\SourceBaseFactory;
use Gomeplus\Comx\Rest\RequestMessage;

class DecorTestBase extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Context
     */
    protected $comxContext;

    protected function setUp()
    {
        parent::setUp();
        $sourceBaseFactory = SourceBaseFactory::fromConf(Config::fromArray([
            'sourceBases' => [
                [
                    'id'=>SourceBaseFactory::DEFAULT_BASE_ID,
                    'urlPrefix' => COMX_TEST_ATOMIC_URL_PREFIX,
                ],
                [
                    'id' => 'sub',
                    'urlPrefix' => COMX_TEST_ATOMIC_URL_PREFIX.'/sub',
                ]
            ]
        ]));
        $this->comxContext = new Context();
        $this->comxContext
            ->setUrlPrefix('http://a.com')
            ->setSourceBaseFactory($sourceBaseFactory)
            ->setRequest(new RequestMessage('http://a.com/a?b=1'));
    }
}