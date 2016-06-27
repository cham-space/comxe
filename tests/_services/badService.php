<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/30
 * Time: 下午3:03
 */

header('Content-Type: application/json; charset=UTF-8');

if ($_GET['type'] == 'badJson') {
    echo "{";
}

if ($_GET['type'] == 'invalidMessage')
{
    echo json_encode(["data"=>["id"=>1]]);
}

if ($_GET['type'] == 'invalidMessage1')
{
    echo json_encode(["message"=>"x"]);
}

if ($_GET['type'] == '404')
{
    header('HTTP/1.1 404 Not Found');
    echo json_encode(["message"=>"资源不存在", "data"=>(object)[]]);
}
