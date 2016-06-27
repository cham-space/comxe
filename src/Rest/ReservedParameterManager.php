<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/28
 * Time: 下午7:11
 */

namespace Gomeplus\Comx\Rest;


class ReservedParameterManager
{
    const RESERVED_PARAMETERS = [
        'loginToken' => 'X-Gomeplus-Login-Token',
        'userId' => 'X-Gomeplus-User-Id',
        'accessToken' => 'X-Gomeplus-Access-Token',
        'device' => 'X-Gomeplus-Device',
        'app' => 'X-Gomeplus-App',
        'net' => 'X-Gomeplus-Net',
        'accept' => 'Accept',
        'traceId' => 'X-Gomeplus-Trace-Id'
    ];

    public static function fromRequest(RequestMessage $request) {
        $headers = $request->getHeaderParameters();
        $queries = $request->getUrl()->getQuery()->getParameters();
        $reservedParamsFromHeader = [];
        $reservedParamsFromQuery = [];
        foreach (self::RESERVED_PARAMETERS as $parameterName =>  $headerName) {
            if (isset($headers[$headerName])) {
                $reservedParamsFromHeader[$parameterName] = $headers[$headerName];
            }
            if (isset($queries[$parameterName])) {
                $reservedParamsFromQuery[$parameterName] = $queries[$parameterName];
            }
        }
        return new self($reservedParamsFromQuery, $reservedParamsFromHeader);
    }

    protected $paramsFromQuery;

    protected $paramsFromHeader;

    public function __construct($paramsFromQuery, $paramsFromHeader)
    {
        $this->paramsFromHeader = $paramsFromHeader;
        $this->paramsFromQuery = $paramsFromQuery;
    }

    /**
     * @return array
     */
    public function getReservedQueryParams()
    {
        return $this->paramsFromQuery;
    }

    /**
     * @return array
     */
    public function getFilteredReservedHeaders()
    {
        $result = [];
        foreach ($this->paramsFromHeader as $k => $v) {
            if (isset($this->paramsFromQuery[$k])) {
                continue;
            }
            $result[self::RESERVED_PARAMETERS[$k]] = $v;
        }
        return $result;
    }
}