<?php
/**
 * User: zhaoqing
 * Date: 16/3/29
 * Time: 下午10:48
 */

assert_options(ASSERT_CALLBACK, function($file, $line, $v, $message) {
    is_null($file);
    is_null($line);
    is_null($v);
    throw new \RuntimeException('fail to assert '.$message);
});

require __DIR__ . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";