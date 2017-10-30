<?php
// +----------------------------------------------------------------------
// | [RhaPHP System] Copyright (c) 2017 http://www.rhaphp.com/
// +----------------------------------------------------------------------
// | [RhaPHP] 并不是自由软件,你可免费使用,未经许可不能去掉RhaPHP相关版权
// +----------------------------------------------------------------------
// | Author: Geeson <qimengkeji@vip.qq.com>
// +----------------------------------------------------------------------

namespace app\behavior;


use think\Config;
use think\Request;

class ChangeTheme
{

    public function run()
    {
        $defaultViewPath = Config::get('template.view_path');
        if ($defaultViewPath != '') {
            $module = strtolower(Request::instance()->module());
            $pcPath = Config::get('theme.pc');
            $mobilePath = Config::get('theme.mobile');
            if (Request::instance()->isMobile()) {
                if($mobilePath==''){
                    $themePath = $pcPath;
                }else{
                    $themePath = $mobilePath;
                }
            } else {
                $themePath = $pcPath;
            }
            Config::set('template.view_path', ROOT_PATH.$defaultViewPath . DS . $themePath . DS . $module.DS);
        }
        if(ENTR_PATH==''){
            $path='/public/static';
        }else{
            $path='/static';
        }
        Config::set('view_replace_str.__STATIC__', $path);
    }

}