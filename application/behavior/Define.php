<?php
// +----------------------------------------------------------------------
// | [RhaPHP System] Copyright (c) 2017 http://www.rhaphp.com/
// +----------------------------------------------------------------------
// | [RhaPHP] 并不是自由软件,你可免费使用,未经许可不能去掉RhaPHP相关版权
// +----------------------------------------------------------------------
// | Author: Geeson <qimengkeji@vip.qq.com>
// +----------------------------------------------------------------------


namespace app\behavior;


use think\Request;

class Define
{
    public function run()
    {
        $module = strtolower(Request::instance()->module());
        $Controller = strtolower(Request::instance()->controller());
        $action = strtolower(Request::instance()->action());
        define('MODULE_NAME', $module);
        define('CONTROLLER_NAME', $Controller);
        define('ACTION_NAME', $action);

    }
}