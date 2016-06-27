<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/24
 * Time: 下午11:11
 */

namespace Gomeplus\Comx\Schema\DataDecor;


use Gomeplus\Comx\Context\Context;
use Gomeplus\Comx\Schema\Source\Source;
use Peekmo\JsonPath\JsonStore;

class EachDecor extends AbstractDecor
{
    const FIELD_SOURCE = 'source';
    const FIELD_REF_JSON_PATH = 'refJsonPath';
    const FIELD_FIELD = 'field';
    /**
     * @var Source
     */
    protected $source;
    /**
     * target field
     * @var string
     */
    protected $field;

    /**
     * @param $data
     * @param Context $context
     * @throws \Comos\Config\Exception
     * @return void
     */
    public function doDecorate(&$data, Context $context)
    {
        $matchedNodes = $this->getMatchedNodes($data);
        $this->source = new Source($this->conf->rsub(self::FIELD_SOURCE));;
        $this->field = $this->conf->str(self::FIELD_FIELD);

        $this->logger()->debug(__CLASS__. ' input data', $data);
        foreach ($matchedNodes as &$ref) {
            $this->decorateOneNode($ref, $data , $context);
        }
    }

    protected function getMatchedNodes(&$data) {
        $refJsonPath = $this->conf->str(self::FIELD_REF_JSON_PATH);
        if (empty($refJsonPath)) {
            return [&$data];
        }
        $store = new JsonStore();
        $matchedNodes =  $store->get($data, $refJsonPath);
        $this->logger()->debug(__CLASS__.' matchedNodes '.$refJsonPath, $matchedNodes);
        return $matchedNodes;
    }

    /**
     * @param $ref
     * @param $data
     * @param Context $context
     */
    protected function decorateOneNode(&$ref, &$data, Context $context) {
        $loaded = $this->source->loadData($context, ['data'=>$data, 'ref'=>$ref]);
        if (is_null($loaded))
        {
            $this->appendDebugInfo("Ignore null data");
            return;
        }
        if ($this->field) {
            $loaded = [$this->field=>$loaded];
        }
        $ref = array_merge($ref, $loaded);
    }

    public function getType()
    {
        return AbstractDecor::TYPE_EACH;
    }
}