<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/15
 * Time: 下午7:12
 */

header('Content-Type: application/json; charset=UTF-8');

echo json_encode([
    'message' => '',
    'data' => [
        'items' => [
            [
                'id' => 11,
                'title' => 'a',
                'userId' => 1,
            ],
            [
                'id' => 12,
                'title' => 'b',
                'userId' => 2,
            ]
        ]
    ]
], JSON_UNESCAPED_UNICODE);