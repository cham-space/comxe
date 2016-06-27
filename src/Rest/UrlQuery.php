<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/14
 * Time: 上午10:50
 */

namespace Gomeplus\Comx\Rest;


class UrlQuery implements \ArrayAccess
{
    /**
     * @var string
     */
    private $queryString;
    /**
     * @var array
     */
    private $parsedParameters;

    /**
     * @param string $queryString
     */
    public function __construct($queryString)
    {
        $this->queryString = $queryString;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->queryString;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        $this->parse();
        return $this->parsedParameters;
    }

    /**
     * lazy loading
     */
    protected function parse()
    {
        if (!is_null($this->parsedParameters)) {
            return;
        }

        $result = [];
        parse_str($this->queryString, $result);
        $this->parsedParameters = $result;
    }

    /**
     * @param $name
     * @return array
     */
    public function getParameter($name)
    {
        $this->parse();
        return isset($this->parsedParameters[$name]) ? $this->parsedParameters[$name] : null;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return !is_null($this->getParameter($offset));
    }

    public function offsetGet($offset)
    {
        return $this->getParameter($offset);
    }

    public function offsetSet($offset, $value)
    {
        return;
    }

    public function offsetUnset($offset)
    {
        return;
    }
}