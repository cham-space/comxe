<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/18
 * Time: 下午3:28
 */

namespace Gomeplus\Comx\Context\SourceBase;

use Comos\Config\Config;
use Gomeplus\Comx\Context\Context;
use Gomeplus\Comx\Debug\DebugTrait;
use Gomeplus\Comx\Log\LogTrait;
use Gomeplus\Comx\Rest\RequestMessage;
use Gomeplus\Comx\Rest\ReservedParameterManager;
use Gomeplus\Comx\Rest\Url;
use Gomeplus\Comx\Schema\Source\Source;
use Gomeplus\Comx\Schema\Source\UnmatchedRequestMethodException;
use Gomeplus\Comx\Schema\TinyTemplate;

abstract class RequestBasedSourceBaseAbstract extends SourceBaseAbstract
{
    use DebugTrait;

    use LogTrait;

    public function executeLoading(Context $context, Config $sourceOptions, $reservedVariablesForUriTemplate)
    {
        $method = $sourceOptions->str(Source::FIELD_METHOD, RequestMessage::METHOD_GET);
        $currentRequest = $context->getRequest();
        $reservedParamMan = ReservedParameterManager::fromRequest($currentRequest);
        $targetUrl = $this->getResourceUrl(
            $context,
            $sourceOptions->rstr(Source::FIELD_URI),
            $reservedVariablesForUriTemplate,
            $reservedParamMan->getReservedQueryParams()
        );

        $this->appendDebugInfo("Load data: " . $targetUrl, ['base'=>$this->getId()]);

        $this->logger()->debug('Source load remote data', [Source::FIELD_METHOD => $method, 'url' => $targetUrl]);

        $requestData = null;
        if ($method == RequestMessage::METHOD_POST || $method == RequestMessage::METHOD_PUT) {
            if ($currentRequest->getMethod() != $method) {
                throw new UnmatchedRequestMethodException('current method is ' . $context->getRequest()->getMethod());
            }
            $requestData = $currentRequest->getData();
        }

        $request = new RequestMessage(
            $targetUrl,
            $method,
            $requestData,
            $reservedParamMan->getFilteredReservedHeaders(),
            $sourceOptions->int(Source::FIELD_TIMEOUT, Source::DEFAULT_TIMEOUT)
        );

        return $this->doRequest($request, $context);
    }

    /**
     * @param $context
     * @param $uri
     * @param $reservedVariablesForUriTemplate
     * @param $reservedQueryParams
     * @return Url
     */
    protected function getResourceUrl($context, $uri, $reservedVariablesForUriTemplate, $reservedQueryParams)
    {

        $tpl = new TinyTemplate($uri);
        $renderedUri = $tpl->render($reservedVariablesForUriTemplate);
        //TODO 进行更严格的判断
        if (strpos($renderedUri, '://')) {
            return $renderedUri;
        }

        return Url::fromUrl($this->getUrlPrefix($context) . $renderedUri)->mergeQueryParameters($reservedQueryParams);
    }

    abstract protected function doRequest(RequestMessage $request, Context $context);

    abstract public function getUrlPrefix(Context $context);


}