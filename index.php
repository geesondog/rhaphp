<?php
// +----------------------------------------------------------------------
// | [RhaPHP System] Copyright (c) 2017 http://www.rhaphp.com/
// +----------------------------------------------------------------------
// | [RhaPHP] 并不是自由软件,你可免费使用,未经许可不能去掉RhaPHP相关版权
// +----------------------------------------------------------------------
// | Author: Geeson <qimengkeji@vip.qq.com>
// +----------------------------------------------------------------------

if (version_compare(PHP_VERSION, '5.5.0', '<'))
    die('require PHP > 5.5.0 !');
define('ENTR_PATH','');
define('APP_PATH', __DIR__ . '/application/');
define('ADDON_PATH', __DIR__ . '/addons/');
define('ADDON_ROUTE','/app/');
define('RUNTIME_PATH', __DIR__ . '/data/runtime/');
require __DIR__ . '/thinkphp/start.php';


