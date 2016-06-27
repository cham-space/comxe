<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/18
 * Time: 下午2:40
 */

namespace Gomeplus\Comx\Context\SourceBase;


use Gomeplus\Comx\Context\Context;
use Gomeplus\Comx\Rest\RequestMessage;

class InnerSourceBase extends RequestBasedSourceBaseAbstract
{
    /**
     * @param RequestMessage $request
     * @param Context $context
     * @return array|null
     * @throws \Gomeplus\Comx\Rest\Exception
     * @throws \Gomeplus\Comx\Schema\CannotFindSchemaException
     * @throws \Gomeplus\Comx\Schema\SecurityException
     */
    protected function doRequest(RequestMessage $request, Context $context)
    {
        $newContext = clone($context);
        $newContext->setRequest($request);
        $path = $request->getUrl()->getRelatedPath($newContext->getUrlPrefix());
        $data = [];
        $newContext->getSchemaLoader()->load($path)->decorate($data, $newContext);
        if (empty($data))
        {
            return null;
        }
        return $data;
    }

    public function getUrlPrefix(Context $context)
    {
        return $context->getUrlPrefix();
    }
}