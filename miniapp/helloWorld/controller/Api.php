<?php
namespace miniapp\helloWorld\controller;


use app\common\controller\MiniappAddon;

class Api extends MiniappAddon
{
    public $isCheckLogin = true;//开启登录验证

    public function initialize()
    {
        parent::initialize();
        $rd_session = input('rd_session');//key可自定：可以使用token、session_id,我在此使用rd_session
        if ($rd_session) {
            //如果存在赋值 $this->rd_session
            $this->rd_session = $rd_session;
        }
    }

    public function index()
    {
        //返回小程序
        return json(['rd_session' => $this->rd_session,'userInfo' => $this->userinfo]);
    }

}