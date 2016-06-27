<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/8
 * Time: 下午1:01
 */


sleep($_GET['sleepingTime']);

header('Content-Type: application/json; charset=UTF-8');

echo json_encode([
    'message'=>'',
    'sleepingTime' =>  (int)$_GET['sleepingTime'],
]);