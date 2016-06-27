<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/24
 * Time: 上午12:25
 */

namespace Gomeplus\Comx\Rest;


class HttpResponseException extends \Exception
{
    protected $userMessage;

    public function __construct($code, $userMessage = null, $previousException = null)
    {
        $this->userMessage = $userMessage;
        parent::__construct(HttpCodes::getMessage($code), $code, $previousException);
    }

    public function getUserMessage()
    {
        return $this->userMessage;
    }
}