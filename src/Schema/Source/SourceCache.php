<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/9
 * Time: 下午9:27
 */

namespace Gomeplus\Comx\Schema\Source;

use Comos\Config\Config;
use Gomeplus\Comx\Context\Context;
use Gomeplus\Comx\Debug\DebugTrait;
use Gomeplus\Comx\Schema\DataDecor\NullSourceCache;
use Gomeplus\Comx\Schema\TinyTemplate;

class SourceCache
{
    use DebugTrait;

    const FIELD_KEY = 'key';

    const FIELD_TTL = 'ttl';

    const KEY_PREFIX = 'Source:';

    protected $key;

    protected $ttlMs;

    /**
     * @param Config $conf
     * @param $params
     * @param $defaultKeyTpl
     * @return NullSourceCache|SourceCache
     * @throws \Comos\Config\Exception
     */
    public static function create(Config $conf, $params, $defaultKeyTpl)
    {
        if (empty($conf->rawData())) {
            return new NullSourceCache();
        }

        $keyTpl = $conf->str(self::FIELD_KEY, $defaultKeyTpl);
        $tpl = new TinyTemplate($keyTpl);
        $key = self::KEY_PREFIX.$tpl->render($params);
        return new self($key, $conf->rint(self::FIELD_TTL));
    }

    /**
     * Cache constructor.
     * @param $key
     * @param $ttlMs
     */
    protected function __construct($key, $ttlMs)
    {
        $this->key = $key;
        $this->ttlMs = $ttlMs;
    }

    public function get(Context $context)
    {
        $result = $context->getCache()->get($this->key);
        if (!is_null($result)) {
            $this->appendDebugInfo('Cache got: '.$this->key);
        } else {
            $this->appendDebugInfo('Cache missed: '.$this->key);
        }
        return $result;
    }

    public function set(Context $context, $data)
    {
        $this->appendDebugInfo('Cache updated: '.$this->key);
        $context->getCache()->set($this->key, $data, $this->ttlMs);
    }
}