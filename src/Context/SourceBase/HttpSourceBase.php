<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/15
 * Time: 下午8:53
 */

namespace Gomeplus\Comx\Context\SourceBase;

use Gomeplus\Comx\Context\Context;
use Gomeplus\Comx\Rest\RequestMessage;

class HttpSourceBase extends RequestBasedSourceBaseAbstract
{

    const FIELD_URL_PREFIX = 'urlPrefix';

    public function getUrlPrefix(Context $context)
    {
        return $this->conf->rstr(self::FIELD_URL_PREFIX);
    }


    /**
     * @param RequestMessage $request
     * @param Context $context
     * @return mixed
     */
    protected function doRequest(RequestMessage $request, Context $context)
    {
        return $request->execute()->getData();
    }


}
