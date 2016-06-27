<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/13
 * Time: 上午7:09
 */

header('Content-Type: application/json; charset=UTF-8');

echo json_encode([
    'data' => [
        'result' => $_GET['origin'] + 1,
    ],
    'message'=> '',
], JSON_UNESCAPED_UNICODE);