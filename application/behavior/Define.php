<?php
// +----------------------------------------------------------------------
// | [RhaPHP System] Copyright (c) 2017 http://www.rhaphp.com/
// +----------------------------------------------------------------------
// | [RhaPHP] 并不是自由软件,你可免费使用,未经许可不能去掉RhaPHP相关版权
// +----------------------------------------------------------------------
// | Author: Geeson <qimengkeji@vip.qq.com>
// +----------------------------------------------------------------------


namespace app\behavior;


use think\facade\Env;
use think\facade\Request;


class Define
{
    public function run()
    {

        $module = strtolower(Request::module());
        $Controller = strtolower(Request::controller());
        $action = strtolower(Request::action());
        define('MODULE_NAME', $module);
        define('CONTROLLER_NAME', $Controller);
        define('ACTION_NAME', $action);
        

    }
}