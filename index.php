<?php
// +----------------------------------------------------------------------
// | [RhaPHP System] Copyright (c) 2017-2020 http://www.rhaphp.com/
// +----------------------------------------------------------------------
// | [RhaPHP] 并不是自由软件,你可免费使用,未经许可不能去掉RhaPHP相关版权
// +----------------------------------------------------------------------
// | Author: Geeson <qimengkeji@vip.qq.com>
// +----------------------------------------------------------------------
namespace think;
//如果出现一片空白，请检查PHP版本
//环境要求：PHP>=5.6 mysql 建议在5.5以上
define('ENTR_PATH','');
define('ROOT_PATH',__DIR__.'/');
define('DS',DIRECTORY_SEPARATOR);
define('APP_PATH', __DIR__ . '/application/');
define('ADDON_PATH', __DIR__ . '/addons/');
define('MINIAPP_PATH', __DIR__ . '/miniapp/');
define('ADDON_ROUTE','/app/');
define('EXTEND_PATH',ROOT_PATH . 'extend/');
require __DIR__ . '/thinkphp/base.php';
Container::get('app',[APP_PATH])->run()->send();
