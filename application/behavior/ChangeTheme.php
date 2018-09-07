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
use think\facade\View;

class ChangeTheme
{
    public function run()
    {
        $root_path = Env::get('root_path');
        $model = Request::module();
        if (Request::isMobile()) {
            $view_path = $root_path . 'themes/mobile/' . $model . '/';
        } else {
            $view_path = $root_path . 'themes/pc/' . $model . '/';
        }
        View::config('view_path', $view_path);
    }
}