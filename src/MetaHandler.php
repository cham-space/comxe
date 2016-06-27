<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/25
 * Time: 下午12:37
 */

namespace Gomeplus\Comx;


use Gomeplus\Comx\Context\Context;
use Gomeplus\Comx\Schema\DataDecor\RootDecor;

class MetaHandler
{
    public function handle(Context $context)
    {
        $apis = [];
        $decors = $context->getSchemaLoader()->getAllApis();
        foreach ($decors as $decor)
        {
            /**
             * @var RootDecor $decor
             */
            $meta = $decor->getMeta();
            $uri = $meta->sub('uri');
            $apis[] = [
                'name' => $meta->str('name', ''),
                'module' => $meta->str('module', ''),
                'uri' => [
                    'path' => $uri->str('path', ''),
                    'parameters' => $uri->sub('parameters')->rawData()
                ],
                'memo' => $meta->str('memo', ''),
                'method'=>$meta->str('method'),
            ];
        }
        $data = [
            'apis' => $apis
        ];
        $context->getResponse()->setData($data);
    }
}