<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/1
 * Time: 下午3:47
 */
header('Content-Type: application/json; charset=UTF-8');

$bodyStr = '';
$body = [];

if ($_SERVER['REQUEST_METHOD'] == 'PUT' || $_SERVER['REQUEST_METHOD'] == 'POST')
{
    $bodyStr = file_get_contents('php://input');
    $body = json_decode($bodyStr, true);
}

echo json_encode([
    "message" => "",
    "data" => [
        'server' => $_SERVER,
        'get' => $_GET,
        'bodyStr' => $bodyStr,
        'body' => $body,
    ],
], JSON_UNESCAPED_UNICODE);