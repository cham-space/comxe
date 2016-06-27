<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/24
 * Time: 下午11:56
 */

namespace Gomeplus\Comx\Schema\DataDecor;


use Gomeplus\Comx\Context\Context;
use Gomeplus\Comx\Schema\Source\Source;
use Peekmo\JsonPath\JsonStore;

/**
 * Class BatchDecor
 * @package Gomeplus\Comx\Schema\DataDecor
 * The conf looks like:
 * <pre>
 * {
 * "type": "Batch",
 * "field" : "group",
 * "refJsonPath" : "$.topics.*",
 * "mapping" : {"foreignId":"groupId", "refId":"id"},
 * "source": {
 *  "uri": "/social/groupsByIds?ids={foreignIds}&integrity=simple",
 *  "jsonPath" : "$.groups.*"
 * }
 * }
 * </pre>
 */
class BatchDecor extends AbstractDecor
{

    public function doDecorate(&$data, Context $context)
    {

        $mapping = $this->conf->rsub('mapping');
        $foreignIdField = $mapping->rstr('foreignId');
        $refIdField = $mapping->rstr('refId');

        $store = new JsonStore();
        $refJsonPath = $this->conf->rstr('refJsonPath');
        $matchedNodes = $store->get($data, $refJsonPath);

        $foreignIds = self::getForeignIds($matchedNodes, $foreignIdField);

        $source = new Source($this->conf->rsub('source'));
        $loaded = $source->loadData($context, ['data'=>$data, 'foreignIds'=>$foreignIds]);
        $this->logger()->debug(__CLASS__.' '.__METHOD__, ['foreignIds'=>$foreignIds] );
        self::mapData($matchedNodes, $loaded, $foreignIdField, $refIdField, $this->conf->rstr('field'));
    }

    protected static function mapData($matchedNodes, $loaded, $foreignIdField, $refIdField, $targetField)
    {
        //TODO 缺错误处理
        $map = [];
        foreach ($loaded as $v) {
            $map[$v[$refIdField]] = $v;
        }
        foreach ($matchedNodes as &$node)
        {
            if (isset($map[$node[$foreignIdField]])) {
                $node[$targetField] = $map[$node[$foreignIdField]];
            }
        }
    }

    protected static function getForeignIds($matchedNodes, $foreignIdField)
    {
        $ids = [];
        foreach ($matchedNodes as $node) {
            $ids[] = isset($node[$foreignIdField])?$node[$foreignIdField]:null;
        }

        array_filter(array_unique($ids), function($id){return !is_null($id) && $id!=='';});
        return join(',', $ids);
    }

    public function getType()
    {
        return AbstractDecor::TYPE_BATCH;
    }
}