<?php
// +----------------------------------------------------------------------
// | [RhaPHP System] Copyright (c) 2017-2020 http://www.rhaphp.com/
// +----------------------------------------------------------------------
// | [RhaPHP] 并不是自由软件,你可免费使用,未经许可不能去掉RhaPHP相关版权
// +----------------------------------------------------------------------
// | Author: Geeson <qimengkeji@vip.qq.com>
// +----------------------------------------------------------------------

namespace app\miniapp\controller;


use app\common\model\MiniappMsg;

class Entr
{
    public $_mid;
    public function index($_mid){
        if (empty($_GET["signature"]) || empty($_GET["nonce"]) || empty($_mid)) {
            exit('Access denied');
        }
        $this->_mid = $_mid;
        $miniappInfo = getMimiappInfo($this->_mid);
        $options['appid'] = $miniappInfo['appid'];
        $options['appsecret'] = $miniappInfo['appsecret'];
        $options['token'] = $miniappInfo['token'];
        $options['encodingaeskey'] = $miniappInfo['encodingaeskey'];
        $mpObject = getMiniProgramObj($options);
        if ($mpObject->init() == 'success') {
            if(isset($_GET['echostr']) && !empty($_GET['echostr'])){
                echo $_GET["echostr"];
                exit;
            }
        }
        $msgData = $mpObject->getRev();
        $msg['mpid'] = $this->_mid;
        $msg['openid'] = $msgData['FromUserName'];
        $msg['type'] = $msgData['MsgType'];
        $msg['create_time'] = time();
        $model = new MiniappMsg();
        switch ($msgData['MsgType']) {
            case 'text'://文本消息
                $msg['content'] = $msgData['Content'];
                $model->save($msg);
                break;
            case 'image'://图片消息
                $msg['content'] = getHostDomain() . url('mp/Show/image') . '?url=' . urlencode($msgData['PicUrl']);
                $model->save($msg);
                break;
            case 'miniprogrampage'://卡片消息

                break;
            case 'user_enter_tempsession'://进入会话事件

                break;
        }
    }



}