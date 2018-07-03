<?php
// +----------------------------------------------------------------------
// | [RhaPHP System] Copyright (c) 2017 http://www.rhaphp.com/
// +----------------------------------------------------------------------
// | [RhaPHP] 并不是自由软件,你可免费使用,未经许可不能去掉RhaPHP相关版权
// +----------------------------------------------------------------------
// | Author: Geeson <qimengkeji@vip.qq.com>
// +----------------------------------------------------------------------


namespace app\admin\controller;

use think\captcha\Captcha;
use think\Controller;
use think\Db;
use think\facade\Cookie;
use think\facade\Request;
use think\facade\Session;


class Login extends Controller
{
    public function index()
    {
        if (Request::isAjax()) {
            $captcha = new Captcha();
            if(!$captcha ->check(input('verify'),2)){
                return ['code' => -1, 'msg' => '验证码错误'];
            }
            $user_name = Request::post('user_name');
            $password = Request::post('password');
            $result = Db::name('admin')
                ->where(['admin_name' => $user_name,'status'=>1])
                ->find();
            $inPWD = md5($password . $result['rand_str']);
            if (empty($result)) {
                return ['code' => -1, 'msg' => '用户不存在'];
            }
            if ($result['password'] == $inPWD) {
                Db::name('admin')->where(['admin_name' => $user_name])->update(['ip' => Request::ip(), 'last_time' => time()]);
                session('admin', $result);
                Cookie::forever('admin', $result);
                return ['code' => 200, 'msg' => '登录成功!'];
            } else {
                return ['code' => -1, 'msg' => '密码错误'];
            }
        }
        return view('system/login');
    }

    public function out()
    {
        Session::clear('think_');
        Cookie::clear('think_');
        $this->redirect('admin/Login/index');
    }

    public function verify()
    {
        $captcha = new Captcha(['fontSize' => 15]);
        return $captcha->entry(2);
    }
}