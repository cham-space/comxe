<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/3/30
 * Time: 下午6:23
 */

namespace Gomeplus\Comx\Rest;

use Gomeplus\Comx\Log\LogTrait;

class Client
{
    use LogTrait;

    private static function prepareCurlOptions(&$ch, $method, $body = null, $headers = [], $timeout = null)
    {
        $baseHeaders = [
            'Accept' => 'application/json',
            'Accept-Charset' => 'UTF-8',
            'User-Agent'=> 'gomeplus-comx',
        ];

        if (!is_null($body)) {
            $baseHeaders['Content-Type'] = 'application/json; charset=UTF-8';
        }

        $realHeaders = [];
        foreach (array_merge($baseHeaders, $headers) as $k => $v) {
            $realHeaders[] = "$k: $v";
        }

        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => $realHeaders,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_FOLLOWLOCATION => 1,
        ]);

        if (!is_null($timeout)) {
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
        }

        if (!is_null($body)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }
    }

    /**
     * @param $url
     * @param string $method
     * @param null $data
     * @param array $headers
     * @param null $timeout
     * @return ResponseMessage
     * @throws BizResponseException
     * @throws InvalidMessageException
     * @throws MessageDecodingException
     * @throws NoResponseException
     * @throws TimeoutException
     */
    public function request($url, $method = 'get', $data = null, $headers = [], $timeout = null)
    {
        $this->logger()->info('REST Client '.$method , ['URL' => $url]);

        $ch = curl_init($url);
        self::prepareCurlOptions($ch, $method, $data, $headers, $timeout);
        $responseBody = curl_exec($ch);

        if ($responseBody === false) {
            $errorNumber = curl_errno($ch);
            $error = curl_error($ch);
            curl_close($ch);
            if ($errorNumber == CURLE_OPERATION_TIMEOUTED)
            {
                throw new TimeoutException();
            }
            throw new NoResponseException('fail to '.$method.' ' . $url . " error[{$error}] no[{$errorNumber}]");
        }

        $code = intval(curl_getinfo($ch, CURLINFO_HTTP_CODE));
        curl_close($ch);

        $this->logger()->debug('REST Client '. $method, ['URL' => $url, 'RESPONSE_BODY' => $responseBody, 'CODE' => $code,]);

        $json = json_decode($responseBody, true);
        if (!is_array($json)) {
            throw new MessageDecodingException('invalid response with HTTP Code:' . $code . ' URL:' . $url, $code);
        }

        if (!isset($json['message']) || !is_string($json['message'])) {
            throw new InvalidMessageException('invalid message format, message field dose not exist or is not a string');
        }

        if (!isset($json['data']) || !is_array($json['data'])) {
            throw new InvalidMessageException('invalid message format, data field dose not exist or is not an object');
        }

        if ($code >= 400 || $code < 200) {
            throw new BizResponseException($json['message'], $code);
        }
        
        return new ResponseMessage($json['data'], $json['message']);
    }

} 