<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/13
 * Time: 下午8:59
 */

namespace Gomeplus\Comx\Rest;


class Url extends ArrayAccessBase
{

    protected static $defaultPorts = [
        'http' => 80,
        'https' => 443,
    ];
    /**
     * @var string
     */
    protected $url;
    /**
     * @var array
     */
    protected $parsedData;
    /**
     * @var UrlQuery
     */
    private $queryObject;

    protected function getArrayAccessibleFields()
    {
        return ['query' => self::COULD_GET, 'queryString' => self::COULD_GET, 'path' => self::COULD_GET, 'host' => self::COULD_GET,
            'scheme' => self::COULD_GET, 'port' => self::COULD_GET, 'hash' => self::COULD_GET,
            'user' => self::COULD_GET, 'pass' => self::COULD_GET, 'portWithDefaultValue'=>self::COULD_GET];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return strval($this->url);
    }

    /**
     * @param Url|string $url
     */
    protected function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * @return Url
     */
    public static function fromEnv()
    {
        $scheme = 'http://';

        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $scheme = 'https://';
        }

        $host = $_SERVER['SERVER_NAME'];

        $portSection = empty($_SERVER['SERVER_PORT']) ? '' : ':'.$_SERVER['SERVER_PORT'];

        $uri = $_SERVER['REQUEST_URI'];

        $url = $scheme.$host.$portSection.$uri;

        return new self($url);
    }

    /**
     * @param Url|string $url
     * @return Url
     */
    public static function fromUrl($url)
    {
        if ($url instanceof Url) {
            return $url;
        }

        return new Url($url);
    }


    protected function parse()
    {
        if (!is_null($this->parsedData)) {
            return;
        }
        $this->parsedData = parse_url($this->url);
    }

    protected function getField($component)
    {
        $this->parse();
        if (!isset($this->parsedData[$component])) {
            return null;
        }
        return $this->parsedData[$component];
    }

    /**
     * @return UrlQuery
     */
    public function getQuery()
    {
        if (is_null($this->queryObject)) {
            $this->queryObject = new UrlQuery($this->getField('query'));
        }
        return $this->queryObject;
    }

    public function getPath()
    {
        return $this->getField('path');

    }

    public function getHost()
    {
        return $this->getField('host');
    }

    public function getScheme()
    {
        return $this->getField('scheme');
    }

    /**
     * @return null|int
     */
    public function getPort()
    {
        return $this->getField('port');
    }

    /**
     * @return null|int
     */
    public function getPortWithDefaultValue()
    {
        $port = $this->getPort();
        if (!is_null($port)) {
            return $port;
        }

        if (isset(self::$defaultPorts[$this->getScheme()])) {
            return self::$defaultPorts[$this->getScheme()];
        }
        return null;
    }

    public function getHash()
    {
        return $this->getField('fragment');
    }

    public function getPass()
    {
        return $this->getField('pass');

    }

    /**
     * @return string|null
     */
    public function getUser()
    {
        return $this->getField('user');

    }

    /**
     * reserve the original parameters
     * @param array $parameters
     * @return Url
     */
    public function mergeQueryParameters($parameters)
    {
        $originParameters = $this->getQuery()->getParameters();
        $mergedParameters = array_merge($parameters, $originParameters);
        $urlStr = self::regenerateUrlStringWithParametersStr(http_build_query($mergedParameters));
        return new self($urlStr);

    }

    protected function regenerateUrlStringWithParametersStr($parametersStr)
    {
        $str = '';
        if (!is_null($this->getScheme()))
        {
            $str.= $this->getScheme().':';
        }

        $hostSection = '';
        $userSection = '';
        if (!is_null($this->getUser()))
        {
            $userSection = $this->getUser();
        }
        if (!is_null($this->getPass()))
        {
            $userSection .= ':'.$this->getPass();
        }

        if (strlen($userSection)) {
            $hostSection .= $userSection.'@'.$this->getHost();
        } else {
            $hostSection = $this->getHost();
        }

        if (strlen($hostSection)) {
            $hostSection = '//'.$hostSection;
        }

        $str.=$hostSection;

        if (!is_null($this->getPort()))
        {
            $str .= ':'.$this->getPort();
        }

        if (!is_null($this->getPath()))
        {
            $str .= $this->getPath();
        }

        if (strlen($parametersStr)) {
            $str .= '?' . $parametersStr;
        }

        if (!is_null($this->getHash()))
        {
            $str .= '#' . $this->getHash();
        }

        return $str;

    }

    /**
     * @param $urlPrefix
     * @return mixed|null
     * @throws Exception
     */
    public function getRelatedPath($urlPrefix)
    {
        $prefix = self::fromUrl($urlPrefix);

        if ($prefix->getHost() != $this->getHost()) {
            throw new Exception("fail to get related path. unmatched prefix.\tPREFIX[$prefix]\tURL[$this]\tPREFIX_HOST["
                . $prefix->getHost()
                ."\tHOST[".$this->getHost()."]");
        }

        if ($prefix->getPortWithDefaultValue() != $this->getPortWithDefaultValue()) {
            throw new Exception("fail to get related path. unmatched port.\tPREFIX[$prefix]\tURL[$this]");
        }

        $sourcePath = $this->getPath();
        $prefixPath = $prefix->getPath();
        if (!strlen($prefixPath)) {
            return $sourcePath;
        }
        $pos = strpos($sourcePath, $prefixPath);
        if ($pos !== 0) {
            throw new Exception("fail to get related path. unmatched path.\tPREFIX[$prefix]\tURL[$this]");
        }

        return substr($sourcePath, strlen($prefixPath));
    }
}