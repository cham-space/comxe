<?php
namespace Gomeplus\Comx\Context\SourceBase;

use Comos\Config\Config;
use Gomeplus\Comx\Log\LogTrait;

/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/15
 * Time: 下午8:53
 */
class SourceBaseFactory
{

    use LogTrait;

    const DEFAULT_BASE_ID = 'default';
    const FIELD_ATOMIC_URL_PREFIX = 'atomicUrlPrefix';
    const FIELD_SOURCE_BASES = 'sourceBases';
    const SELF_BASE_ID = 'self';
    
    const FIELD_TYPE = 'type';
    const TYPE_HTTP = 'http';
    const TYPE_REDIS = 'redis';

    const SOURCE_BASE_TYPES = [
        self::DEFAULT_TYPE => HttpSourceBase::class,
        self::TYPE_REDIS => RedisSourceBase::class,
    ];

    const DEFAULT_TYPE = 'http';

    /**
     * @var array
     */
    protected $pool;

    /**
     * @param Config $conf The root node of comx.conf.json
     * @return SourceBaseFactory
     * @throws \Comos\Config\Exception
     */
    public static function fromConf(Config $conf)
    {
        $factory = new self();
        if ($defaultAtomicUrlPrefix =$conf->str(self::FIELD_ATOMIC_URL_PREFIX))
        {
            $factory->putSourceBase(
                new HttpSourceBase(
                    Config::fromArray(
                        [HttpSourceBase::FIELD_ID=> self::DEFAULT_BASE_ID,
                            HttpSourceBase::FIELD_URL_PREFIX=>$defaultAtomicUrlPrefix
                        ]
                    )
                )
            );
        }
        $bases = $conf->sub(self::FIELD_SOURCE_BASES);
        foreach ($bases->keys() as $i)
        {
            /** @noinspection PhpUndefinedMethodInspection */
            $conf = $bases->sub($i);
            $factory->putSourceBase(self::populateBaseObject($conf));
        }

        return $factory;
    }

    protected static function populateBaseObject(Config $conf)
    {
        $type = $conf->str(self::FIELD_TYPE, self::DEFAULT_TYPE);
        if(!key_exists($type, self::SOURCE_BASE_TYPES)) {
            throw new UnknownSourceBaseTypeException('unknown source base type: '.$type);
        }
        $clazz = self::SOURCE_BASE_TYPES[$type];
        return new $clazz($conf);
    }

    /**
     * @param SourceBaseAbstract $sourceBase
     */
    public function putSourceBase($sourceBase)
    {
        if ($sourceBase->getId() == self::SELF_BASE_ID) {
            self::logger()->warning('"self" is a reserved base ID.');
            return;
        }
        $this->pool[$sourceBase->getId()] = $sourceBase;
    }

    /**
     * @param $id
     * @return SourceBaseAbstract
     * @throws UndefinedSourceBaseException
     */
    public function getSourceBase($id)
    {
        if (is_null($id) || strlen($id) === 0)
        {
            $id = self::DEFAULT_BASE_ID;
        }

        if (isset($this->pool[$id])) {
            return $this->pool[$id];
        }

        if ($id == self::SELF_BASE_ID)
        {
            return new InnerSourceBase(Config::fromArray(['id'=>self::SELF_BASE_ID]));
        }

        throw new UndefinedSourceBaseException('id: '.$id);
    }

    /**
     * @return SourceBaseAbstract
     * @throws UndefinedSourceBaseException
     */
    public function getDefaultSourceBase()
    {
        return $this->getSourceBase(self::DEFAULT_BASE_ID);
    }
}