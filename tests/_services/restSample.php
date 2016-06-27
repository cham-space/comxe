<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/1
 * Time: 下午3:47
 */
header('Content-Type: application/json; charset=UTF-8');

echo json_encode([
    "message" => "",
    "data" => [
        "id" => 1,
        "name" => "x",
    ],
], JSON_UNESCAPED_UNICODE);