<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/9
 * Time: 上午9:09
 */

namespace Gomeplus\Comx\Cache;

use Comos\Config\Config;

use Gomeplus\Comx\Debug\DebugTrait;
use Gomeplus\Comx\Log\LogTrait;
use Predis\Client;

class RedisCache implements Cache
{
    const MAX_KEY_LENGTH = 255;
    use LogTrait;
    use DebugTrait;

    protected $refreshingEnabled = false;

    public static function fromConf(Config $conf)
    {
        return new self($conf);
    }

    public function enableRefreshing() {
        $this->refreshingEnabled = true;
    }

    /**
     * @var Client
     */
    protected $redisClient;
    
    /**
     * @var Config
     */
    protected $conf;

    /**
     * RedisCache constructor.
     * @param Config $conf
     */
    protected function __construct(Config $conf)
    {
        $this->conf = $conf;
    }

    /**
     * @return Client
     */
    protected function getRedisClient()
    {
        if (!is_null($this->redisClient)) {
            return $this->redisClient;
        }
        $conf = $this->conf;
        return $this->redisClient = new Client($conf->sub('servers')->rawData(), $conf->sub('options')->rawData());
    }

    /**
     * The method is only for unit tests to inject mock client
     * @param Client $predisClient
     */
    public function setRedisClient(Client $predisClient)
    {
        $this->redisClient = $predisClient;
    }

    protected function keyValidate($key)
    {
        if (strlen($key)> self::MAX_KEY_LENGTH)
        {
            throw new Exception('Cache key is to long. Key: '.substr($key, 0, 100).'...');
        }
    }

    /**
     * @param $key
     * @return null|mixed
     */
    public function get($key)
    {
        if ($this->refreshingEnabled)
        {
            return null;
        }

        try {
            $this->keyValidate($key);
            $str = $this->getRedisClient()->get($key);
            if (is_null($str)) {
                return null;
            }
            return unserialize($str);
        } catch (\Exception $ex) {
            $this->logger()->error('Cache.get Exception', ['key'=>$key, 'exception'=>$ex]);
            $this->appendDebugInfo('Cache.get Exception', ['key'=>$key, 'exception'=>$ex]);
        }
        return null;
    }

    public function set($key, $value, $timeoutMs)
    {
        try {
            $this->keyValidate($key);
            $this->getRedisClient()->set($key, serialize($value), 'PX', $timeoutMs);
        } catch (\Exception $ex) {
            $this->logger()->error('Cache.set Exception', ['key'=>$key,'ttl'=>$timeoutMs, 'exception'=>$ex]);
            $this->appendDebugInfo('Cache.set Exception', ['key'=>$key,'ttl'=>$timeoutMs, 'exception'=>strval($ex)]);
        }
    }
}