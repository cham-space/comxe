<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/3/30
 * Time: 下午9:02
 */

namespace Gomeplus\Comx\Rest;


class ResponseMessage
{
    
    private $data;

    private $debug;

    private $message;

    private $code;

    public function __construct($data=null, $message = "", $code = 200)
    {
        if (is_null($data)) {
            $data = (object)[];
        }
        $this->data = $data;
        $this->message = $message;
        $this->code = $code;
    }


    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param $info
     * @return ResponseMessage $this
     */
    public function setDebugInfo($info)
    {
        $this->debug = $info;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDebugInfo()
    {
        return $this->debug;
    }
    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $data
     * @return ResponseMessage
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function send()
    {
        if (!headers_sent()) {
            header('HTTP/1.1 '.$this->code.' '.HttpCodes::getMessage($this->code));
            header('Content-Type: application/json; charset=UTF-8');
        }
        $body = ['message'=>$this->message, 'data'=>$this->data];
        if (isset($this->debug)) {
            $body['debug'] = strval($this->debug);
        }
        echo json_encode($body, JSON_UNESCAPED_UNICODE);
    }
} 