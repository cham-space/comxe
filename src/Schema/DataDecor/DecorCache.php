<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/10
 * Time: 下午9:13
 */

namespace Gomeplus\Comx\Schema\DataDecor;


use Comos\Config\Config;
use Gomeplus\Comx\Context\Context;
use Gomeplus\Comx\Debug\DebugTrait;
use Gomeplus\Comx\Schema\TinyTemplate;

class DecorCache
{
    use DebugTrait;

    const FIELD_KEY = 'key';
    const FIELD_TTL = 'ttl';
    const FIELD_WITH_CHILDREN = 'withChildren';
    const FIELD_IS_GLOBAL = 'isGlobal';

    const KEY_PREFIX = 'Decor:';

    protected $key;

    protected $ttlMs;

    protected $isGlobal;

    protected $withChildren;

    protected function __construct($key, $ttlMs, $withChildren)
    {
        $this->key = $key;
        $this->ttlMs = $ttlMs;
        $this->withChildren = $withChildren;
    }

    public static function fromConf(Config $config, $params, Context $context)
    {
        if (empty($config->rawData())) {
            return new NullDecorCache();
        }

        $keyTpl = new TinyTemplate($config->rstr(self::FIELD_KEY));
        $prefix = self::KEY_PREFIX;
        if ($config->bool(self::FIELD_IS_GLOBAL)) {
            $prefix .= ':';
        } else {
            $prefix .= $context->getRequest()->getUrl()->getPath().':';
        }
        $key = $prefix.$keyTpl->render($params);
        return new self(
            $key,
            $config->rint(self::FIELD_TTL),
            $config->bool(self::FIELD_WITH_CHILDREN)
        );
    }

    public function getWithChildren(Context $context)
    {
        if (!$this->withChildren) {
            return null;
        }
        $data =  $context->getCache()->get($this->key);
        $status = is_null($data) ? 'missed' : 'got';
        $this->appendDebugInfo('DecorCache '.$status, ['key'=>$this->key]);
        return $data;
    }

    public function getWithoutChildren(Context $context)
    {
        if ($this->withChildren) {
            return null;
        }
        $data = $context->getCache()->get($this->key);
        $status = is_null($data) ? 'missed' : 'got';
        $this->appendDebugInfo('DecorCache '.$status, ['key'=>$this->key]);
        return $data;
    }

    public function setBeforeChildren(Context $context, $data)
    {
        if ($this->withChildren) {
            return;
        }
        $this->appendDebugInfo('DecorCache setBeforeChildren', ['key'=>$this->key, 'ttl'=>$this->ttlMs]);
        $context->getCache()->set($this->key, $data, $this->ttlMs);
    }

    public function setAfterChildren(Context $context, $data)
    {
        if (!$this->withChildren) {
            return;
        }

        $this->appendDebugInfo('DecorCache setAfterChildren', ['key'=>$this->key, 'ttl'=>$this->ttlMs]);
        $context->getCache()->set($this->key, $data, $this->ttlMs);
    }
}