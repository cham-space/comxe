<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/15
 * Time: 下午7:12
 */

header('Content-Type: application/json; charset=UTF-8');

if ($_GET['id'] < 0) {
    header('HTTP/1.1 422 Invalid');
    echo json_encode([
        'message' => 'invalid param id',
        'data' => (object)[]]);
    exit;
}

echo json_encode([
    'message' => '',
    'data' =>
        [
            'id' => intval($_GET['id']),
            'name' => 'n' . $_GET['id'],
        ]
], JSON_UNESCAPED_UNICODE);