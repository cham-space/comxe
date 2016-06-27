<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/19
 * Time: 下午12:13
 */

namespace Gomeplus\Comx;

use Gomeplus\Comx\Context\Context;
use Gomeplus\Comx\Log\LogTrait;
use Gomeplus\Comx\Rest\BizResponseException;
use Gomeplus\Comx\Rest\HttpResponseException;
use Gomeplus\Comx\Schema\CannotFindSchemaException;

class Handler {

    use LogTrait;

    /**
     * @param Context $context
     * @throws HttpResponseException
     */
    public function handle(Context $context) {
        try {
            $url = $context->getRequest()->getUrl();

            $this->logger()->info("SOURCE_URL", ['URL' => strval($url)]);

            $path = $url->getRelatedPath($context->getUrlPrefix());
            $decor = $context->getSchemaLoader()->load($path, $context->getRequest()->getMethod());
            $data = [];
            $decor->decorate($data, $context);
            $context->getResponse()->setData((object)$data);
        } catch (BizResponseException $ex) {
            throw new HttpResponseException($ex->getCode(), $ex->getMessage(), $ex);
        } catch (CannotFindSchemaException $ex) {
            throw new HttpResponseException(403, '您访问的服务暂不开放,请咨询客服人员.', $ex);
        } catch (\Exception $ex) {
            throw new HttpResponseException(500, '系统繁忙,请稍后再试.', $ex);
        }
    }
}