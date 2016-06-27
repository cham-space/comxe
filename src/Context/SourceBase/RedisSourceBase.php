<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/19
 * Time: 下午12:40
 */

namespace Gomeplus\Comx\Context\SourceBase;

use Comos\Config\Config;
use Gomeplus\Comx\Context\Context;
use Gomeplus\Comx\Log\LogTrait;
use Gomeplus\Comx\Rest\HttpResponseException;
use Gomeplus\Comx\Rest\MessageDecodingException;
use Gomeplus\Comx\Schema\Source\Source;
use Gomeplus\Comx\Schema\TinyTemplate;
use Predis\Client;

class RedisSourceBase extends SourceBaseAbstract
{
    use LogTrait;
    /**
     * @var Client;
     */
    protected $redisClient;

    const FIELD_REDIS = 'redis';

    const FIELD_REDIS_SERVERS = 'servers';

    const FIELD_REDIS_OPTIONS = 'options';

    /**
     * @return Client
     */
    protected function getRedisClient()
    {
        if (!is_null($this->redisClient)) {
            return $this->redisClient;
        }
        $conf = $this->conf->sub(self::FIELD_REDIS);
        return $this->redisClient = new Client(
            $conf->sub(self::FIELD_REDIS_SERVERS)->rawData(),
            $conf->sub(self::FIELD_REDIS_OPTIONS)->rawData()
        );
    }

    public function executeLoading(Context $context, Config $sourceOptions, $reservedVariablesForUriTemplate)
    {
        $uriTpl = $sourceOptions->rstr(Source::FIELD_URI);
        $uri = (new TinyTemplate($uriTpl))->render($reservedVariablesForUriTemplate);
        $key = ''.$uri;
        $result = $this->getRedisClient()->get($key);
        if ($result === null)
        {
            self::appendDebugInfo('fail to get data from Redis.', ['key' => $key]);
            throw new HttpResponseException(404, '获取资源失败');
        }
        $obj = json_decode($result, true);
        if (empty($obj) || !is_array($obj))
        {
            $this->logger()->debug('fail to decode json: '.$result, ['key'=>$key]);
            throw new MessageDecodingException('Fail to decode json from Redis: '.$key);
        }
        return $obj;
    }
}