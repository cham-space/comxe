<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/13
 * Time: 下午4:44
 */

namespace Gomeplus\Comx\Rest;

class RequestMessage extends ArrayAccessBase
{
    const METHOD_GET = 'get';

    const METHOD_POST = 'post';

    const METHOD_DELETE = 'delete';

    const METHOD_PUT = 'put';

    const ACCEPTED_METHODS = [self::METHOD_PUT, self::METHOD_GET, self::METHOD_POST, self::METHOD_DELETE];

    /**
     * @var Client
     */
    protected static $restClient;
    /**
     * @var Url
     */
    protected $url;
    /**
     * @var object
     */
    protected $data;
    /**
     * @var array
     */
    protected $headerParameters;
    /**
     * @var string
     */
    protected $method;
    /**
     * @var int ms
     */
    protected $timeout;

    /**
     * RequestMessage constructor.
     * @param $url
     * @param string $method
     * @param array|null $data
     * @param array $headerParameters
     * @param int|null $timeout
     */
    public function __construct(
        $url,
        $method = self::METHOD_GET,
        $data = null,
        $headerParameters = [],
        $timeout = null
    )
    {
        $this->url = Url::fromUrl($url);
        $this->data = $data;
        $this->headerParameters = $headerParameters;
        $this->method = $method;
        $this->timeout = $timeout;
    }

    /**
     * @return RequestMessage
     * @throws HttpResponseException
     */
    public static function fromEnv() {
        $url = Url::fromEnv();
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        if (!in_array($method, self::ACCEPTED_METHODS)) {
            throw new HttpResponseException(405);
        }
        if ($method != self::METHOD_POST && $method != self::METHOD_PUT) {
            return new self($url, $method, null, self::getHeadersFromEnv());
        }

        $rawData = file_get_contents('php://input');
        $data = @json_decode($rawData, true);
        if (!is_array($data)) {
            throw new HttpResponseException(400);
        }

        return new self($url, $method, $data, self::getHeadersFromEnv());
    }

    public static function getHeadersFromEnv($vars = null)
    {
        if (is_null($vars)) {
            $vars = $_SERVER;
        }
        $prefix = 'HTTP_';
        $headers = [];
        foreach ($vars as $k=>$v) {
            if (strpos($k, $prefix) !== 0) {
                continue;
            }
            $sections = explode('_', $k);
            array_shift($sections);
            $field = join('-', array_map('ucfirst', array_map('strtolower', $sections)));
            $headers[$field] = $v;
        }
        return $headers;
    }

    /**
     * @return Url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param $url
     * @return RequestMessage
     */
    public function setUrl($url)
    {
        $this->url = Url::fromUrl($url);
        return $this;
    }

    /**
     * @param int $timeoutMs
     * @return RequestMessage
     */
    public function setTimeout($timeoutMs)
    {
        $this->timeout = $timeoutMs;
        return $this;
    }

    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return object
     */
    public function getData()
    {
        return $this->data;

    }

    /**
     * @param array $data
     * @return RequestMessage $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function getHeaderParameters()
    {
        return $this->headerParameters;

    }

    /**
     * @return array
     */
    protected function getArrayAccessibleFields()
    {
        return [
            'url' => self::COULD_GET,
            'method' => self::COULD_GET,
            'data' => self::COULD_GET,
            'headerParameters' => self::COULD_GET,
        ];
    }

    /**
     * @return ResponseMessage
     */
    public function execute()
    {
        if (!self::$restClient) {
            self::$restClient = new Client();
        }

        $data = null;
        
        if ($this->getMethod() == self::METHOD_POST || $this->getMethod() == self::METHOD_PUT) {
            $data = json_encode($this->getData(), JSON_UNESCAPED_UNICODE);
        }

        return self::$restClient->request($this->getUrl(), $this->getMethod(), $data, $this->getHeaderParameters(), $this->timeout);
    }
}