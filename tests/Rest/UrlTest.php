<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/13
 * Time: 下午11:32
 */

namespace Gomeplus\Comx\Rest;


class UrlTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var Url
     */
    protected $theUrl, $theUrl1;
    public function test__ToString()
    {
        $url = 'https://gomeplus.com/';
        $urlObj = Url::fromUrl($url);
        $this->assertEquals($url, strval($urlObj));
    }

    public function testFromUrlObject()
    {
        $url = 'https://gomeplus.com/';
        $urlObj = Url::fromUrl($url);
        $urlObj1 = Url::fromUrl($urlObj);
        $this->assertEquals($url, strval($urlObj1));
    }

    protected function setUp() {
        parent::setUp();
        $this->theUrl = Url::fromUrl('https://gomeplus.com/v2/social/group?a=1&b=2&c=3#x');
        $this->theUrl1 = Url::fromUrl('http://alex:pwd@gomeplus.com:88/v2/social/group?a=1&b=2&c=3');
    }

    public function testMergeQueryParameters()
    {
        $newUrl = $this->theUrl->mergeQueryParameters(['z'=>1]);
        $this->assertEquals('https://gomeplus.com/v2/social/group?z=1&a=1&b=2&c=3#x', $newUrl->__toString());

        $newUrl = $this->theUrl1->mergeQueryParameters(['z'=>1]);
        $this->assertEquals('http://alex:pwd@gomeplus.com:88/v2/social/group?z=1&a=1&b=2&c=3', $newUrl->__toString());

        $newUrl = $this->theUrl->mergeQueryParameters(['z'=>1, 'y'=>2, 'a'=>'z']);
        $this->assertEquals('https://gomeplus.com/v2/social/group?z=1&y=2&a=1&b=2&c=3#x', $newUrl->__toString());


        $url = Url::fromUrl('/x3?a=123');
        $new = $url->mergeQueryParameters(['a'=>'bbb']);
        $this->assertEquals('/x3?a=123', strval($new));

        $url = Url::fromUrl('//abc.com:8801/?a=123');
        $new = $url->mergeQueryParameters(['a'=>'bbb']);
        $this->assertEquals('//abc.com:8801/?a=123', strval($new));

        $url = Url::fromUrl('//alex@abc.com:8801/?a=123&b=z');
        $new = $url->mergeQueryParameters(['a'=>'bbb']);
        $this->assertEquals('//alex@abc.com:8801/?a=123&b=z', strval($new));
    }

    public function testGetScheme()
    {
        $this->assertEquals('https', $this->theUrl->getScheme());
        $this->assertEquals('https', $this->theUrl['scheme']);
    }

    public function testGetHost()
    {
        $this->assertEquals('gomeplus.com', $this->theUrl->getHost());
        $this->assertEquals('gomeplus.com', Url::fromUrl('//gomeplus.com/a')->getHost());
        $this->assertEquals('gomeplus.com', $this->theUrl['host']);
    }

    public function testGetPath()
    {
        $this->assertEquals('/v2/social/group', $this->theUrl->getPath());
        $this->assertEquals('/v2/social/group', $this->theUrl['path']);
    }

    public function testGetHash()
    {
        $this->assertEquals('x', $this->theUrl->getHash());
        $this->assertEquals('x', $this->theUrl['hash']);

        $this->assertNull($this->theUrl1['hash']);
    }

    public function testGetUser()
    {
        $this->assertEquals('alex', $this->theUrl1['user']);
        $this->assertEquals('alex', $this->theUrl1->getUser());

        $this->assertNull($this->theUrl['user']);
        $this->assertNull($this->theUrl->getUser());
    }

    public function testGetPass()
    {
        $this->assertEquals('pwd', $this->theUrl1['pass']);
        $this->assertEquals('pwd', $this->theUrl1->getPass());
    }

    public function testGetQuery()
    {
        $this->assertInstanceOf(UrlQuery::class, $this->theUrl1->getQuery());
        $this->assertTrue($this->theUrl1->getQuery() === $this->theUrl1['query']);
        $this->assertEquals('1', $this->theUrl['query']['a']);
    }

    public function testGetPort()
    {
        $this->assertNull($this->theUrl->getPort());
        $this->assertNull($this->theUrl['port']);
        $this->assertEquals(88, $this->theUrl1['port']);
    }

    public function testGetPortWithDefaultValue()
    {
        $this->assertEquals(443, $this->theUrl->getPortWithDefaultValue());
        $this->assertEquals(443, $this->theUrl['portWithDefaultValue']);
        $this->assertEquals(88, $this->theUrl1->getPortWithDefaultValue());
        $this->assertEquals(88, $this->theUrl1['portWithDefaultValue']);
        $this->assertEquals(80, Url::fromUrl('http://x.com/xx')->getPortWithDefaultValue());
        //unknown
        $this->assertNull(Url::fromUrl('ftp://x.com/xx')->getPortWithDefaultValue());
    }

    public function testGetRelatedPath()
    {
        $thePrefix = 'http://gomeplus.com/v2';
        $this->assertEquals("/social/group", Url::fromUrl('http://gomeplus.com/v2/social/group')->getRelatedPath($thePrefix));


        $thePrefix = 'https://api.gomeplus.com';
        $this->assertEquals('/a/b', Url::fromUrl('https://api.gomeplus.com/a/b')->getRelatedPath($thePrefix));
    }
}