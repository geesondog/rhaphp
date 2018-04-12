<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});
Route::rule(ADDON_ROUTE.':addon/:col/:act', '\\app\\mp\controller\\Call@run');
Route::rule('/api/:_mid/:addon/:col/:act', '\\app\\miniapp\controller\\Call@run');
Route::rule('miniprogram/:_mid', 'miniapp/entr/index');
return [

];
