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
    'data' =>
        [
            'id' => intval($_GET['id']),
            'title' => 't' . $_GET['id'],
        ]
], JSON_UNESCAPED_UNICODE);