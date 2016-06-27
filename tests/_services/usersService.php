<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/5/16
 * Time: 下午9:50
 */

header('Content-Type: application/json; charset=UTF-8');

$ids = explode(',', $_GET['ids']);
$users = [];
foreach ($ids as $id) {
    $users[] = [
        'id' => intval($id),
        'name' => 'n'.$id,
    ];
}

echo json_encode(['message'=>'', 'data'=>[
    'users' => $users,
]], JSON_UNESCAPED_UNICODE);