<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/25
 * Time: 下午1:07
 */

namespace Gomeplus\Comx\Schema\DataDecor;

use Comos\Config\Config;

class RootDecor extends CompositionDecor
{
    /**
     * @var string
     */
    private $path;
    /**
     * @var string
     */
    private $method;

    /**
     * @param $path
     * @return RootDecor $this
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @param $method
     * @return RootDecor $this
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return Config
     */
    public function getMeta()
    {
        $metaData = $this->conf->sub('meta')->rawData();
        return Config::fromArray(array_merge_recursive(['method'=>$this->method, 'uri'=>['path'=>$this->path]], $metaData));
    }

    /**
     * @return string
     */
    public function getType()
    {
        return AbstractDecor::TYPE_ROOT;
    }
}