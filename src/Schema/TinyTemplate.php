<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/20
 * Time: 下午1:20
 */

namespace Gomeplus\Comx\Schema;


class TinyTemplate
{

    protected $tpl;

    protected $vars;

    /**
     * TinyTemplate constructor.
     * @param string $tpl
     */
    public function __construct($tpl)
    {
        $this->tpl = $tpl;
    }

    /**
     * @param array $vars
     */
    public function render($vars)
    {
        $this->vars = $vars;
        $result = preg_replace_callback('/\{(.*?)\}/', [$this, '_replaceCallback'], $this->tpl);
        $this->vars = null;
        return $result;
    }

    public function _replaceCallback($matched)
    {
        $varExp = $matched[1];
        $varSections = explode('.', $varExp);

        $matchedValue = $this->vars;
        foreach ($varSections as $vs) {
            if (!isset($matchedValue[$vs])) {
                return null;
            }
            $matchedValue = $matchedValue[$vs];
        }
        return $matchedValue;
    }
}