<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/1
 * Time: 下午4:00
 */

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bootstrap.php';

define('COMX_TEST_HOST', 'localhost');
define('COMX_TEST_PORT', 8090);
define('COMX_TEST_ROOT', __DIR__);
define('COMX_TEST_ATOMIC_URL_PREFIX', 'http://'.COMX_TEST_HOST.':'.COMX_TEST_PORT);

require_once __DIR__.'/DecorTestBase.php';

$script = PHP_BINDIR . DIRECTORY_SEPARATOR . 'php -S ' . COMX_TEST_HOST . ':' . COMX_TEST_PORT . ' -t ' . COMX_TEST_ROOT.'/_services' . ' >/dev/null 2>&1 &';
echo $script . "\n";
exec($script);