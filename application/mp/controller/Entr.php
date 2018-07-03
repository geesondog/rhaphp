<?php
// +----------------------------------------------------------------------
// | [RhaPHP System] Copyright (c) 2017 http://www.rhaphp.com/
// +----------------------------------------------------------------------
// | [RhaPHP] 并不是自由软件,你可免费使用,未经许可不能去掉RhaPHP相关版权
// +----------------------------------------------------------------------
// | Author: Geeson <qimengkeji@vip.qq.com>
// +----------------------------------------------------------------------


namespace app\mp\controller;

use app\common\model\MpFriends;
use app\common\model\MpMsg;
use app\common\model\Qrcode;
use think\Db;

class Entr
{

    private $mid;

    public function index($mid)
    {
        if (empty($_GET['echostr']) && empty($_GET["signature"]) && empty ($_GET["nonce"])) {
            exit('Access denied');
        }
        if (empty($mid) || !is_numeric($mid)) {
            exit;
        }
        $this->mid = $mid;
        if (!$mpInfo = getMpInfo($mid)) {
            exit();
        }
        $options = array(
            'appid' => $mpInfo['appid'],
            'appsecret' => $mpInfo['appsecret'],
            'token' => $mpInfo['valid_token'],
            'encodingaeskey' => $mpInfo['encodingaeskey']
        );
        include_once EXTEND_PATH."wechatSdk/wechat.class.php";
        if (!empty($_GET['echostr']) && !empty($_GET["signature"])) {
            if ($mpInfo['valid_status'] == 0) {
                $model = new \app\common\model\Mp();
                $model->save(['valid_status' => 1], ['id' => $mpInfo['id']]);
            }
            $weObj = new \Wechat($options);
            $weObj->valid();
            exit;
        }
        session('mid',$mid);
        session('mp_options', $options);
        $weObj = new \Wechat($options);
        $weObj->valid();
        $weObj->getRev();//获取微信服务器发来信息(不返回结果)，被动接口必须调用
        $msgData = $weObj->getRevData();//返回微信服务器发来的信息（数组）
        if($mpInfo['status']==0){
            replyText($mpInfo['desc']);exit;
        }
        session('openid', $msgData['FromUserName']);
        $M = new MpFriends();
        $M->updateLastTime($msgData);
        switch ($msgData['MsgType']) {
            case 'text'://文本消息
                $this->keyword($msgData['Content'], $msgData);
                break;
            case 'image'://图片消息
                $this->special('image', $msgData);
                break;
            case 'voice'://语音消息
                $this->special('voice', $msgData);
                break;
            case 'video'://视频消息
                $this->special('video', $msgData);
                break;
            case 'shortvideo'://小视频消息
                $this->special('shortvideo', $msgData);
                break;
            case 'location'://地理位置消息
                $this->special('location', $msgData);
                break;
            case 'link'://链接消息
                $this->special('link', $msgData);
                break;
            case 'event'://事件消息
                switch (strtolower($msgData['Event'])) {
                    case 'subscribe'://关注
                        if (isset($msgData['Ticket'])) {
                            if ($result = Qrcode::get(['ticket' => $msgData['Ticket']])) {//通过生成场景二维码关注
                                //如需要扩展其它，代码在这里开始

                                if (!Db::name('qrcode_data')
                                    ->where(['scene_id' => $result['scene_id'], 'openid' => $msgData['FromUserName']])
                                    ->find()
                                ) {
                                    Db::name('qrcode')
                                        ->where(['scene_id' => $result['scene_id']])
                                        ->setInc('gz_count');
                                }
                                $friendInfo = getFriendInfoForApi(getOrSetOpenid());
                                $friendInfo['tagid_list'] = json_encode($friendInfo['tagid_list']);
                                $friendModel = new MpFriends();
                                if (!empty($friendInfo)) {
                                    $friendInfo['mpid'] = $this->mid;
                                    $Res = $friendModel->where(['mpid' => $this->mid, 'openid' => getOrSetOpenid()])->find();
                                    if (empty($Res)) {
                                        $friendModel->save($friendInfo);
                                    } else {
                                        $friendModel->save($friendInfo, ['mpid' => $this->mid, 'openid' => getOrSetOpenid()]);
                                    }
                                }
                                $this->qrcode($result, $msgData);
                            }
                        }
                        $this->subscribe($msgData);
                        break;
                    case 'unsubscribe'://取关
                        $this->subscribe($msgData, 'unsubscribe');
                        break;
                    case 'scan'://用户扫码已关注时的事件推送
                        if ($result = Qrcode::get(['ticket' => $msgData['Ticket']])) {
                            //注意，没有关过的粉丝第一次扫码关注不走这里
                            //1:通过场景二维码关注
                            $this->qrcode($result, $msgData);
                        }
                        break;
                    case 'location':     // 上报地理位置事件 event_location
                        $this->special('event_location', $msgData);
                        break;
                    case 'click'://自定义菜单事件

                        $this->keyword($msgData['EventKey'], $msgData);
                        break;
                    case 'view'://点击菜单跳转链接时的事件推送
                        $this->special('view', $msgData);
                        break;
                    default:
                        //
                        break;
                }
                break;
            default:
                //
                break;
        }
        //终止执行
       // exit;

    }

    /**
     * 消息处理
     * @author gEESON 314835050@ qq.com
     * @param $msgData
     */
    public function mpMsg($msgData)
    {
        $msg['mpid'] = $this->mid;
        $msg['openid'] = $msgData['FromUserName'];
        $msg['type'] = $msgData['MsgType'];
        $msg['create_time'] = time();
        $model = new MpMsg();
        switch ($msgData['MsgType']) {
            case 'text'://文本消息
                $msg['content'] = $msgData['Content'];
                $model->save($msg);
                break;
            case 'image'://图片消息
                $msg['content'] = getHostDomain() . '/mp/Show/image?url='. urlencode($msgData['PicUrl']);
                $model->save($msg);
                break;
            case 'voice'://语音消息
                $msg['content'] = $msgData['MediaId'];
                $model->save($msg);
                break;
            case 'video'://视频消息
                $msg['content'] = $msgData['MediaId'];
                $model->save($msg);
                break;
            case 'shortvideo'://小视频消息

                break;
            case 'location'://地理位置消息
                $msg['content'] = $msgData['Label'];
                $model->save($msg);
                break;
            case 'link'://链接消息

                break;

        }
        $options=session('mp_options');
        $weObj = new \Wechat($options);
        $weObj->valid();
        $weObj->getRev();
        $weObj->transfer_customer_service()->reply();
    }

    /**
     * 处理特殊事件
     * @author geeson 314835050@qq.com
     * $type 事件类型
     * $msgData 微信服务器发来消息
     */
    public function special($type, $msgData)
    {
        $rule = Db::name('mp_rule')
            ->where(['mpid' => $this->mid, 'event' => $type, 'status' => '1'])->find();
        if (!empty($rule)) {
            if ($rule['keyword']) {
                $this->keyword($rule['keyword'], $msgData);
            }
            if ($rule['addon']) {
                loadAdApi($rule['addon'], $msgData,['mid'=>$this->mid,'addon'=>$rule['addon']]);
            }
        } else {//不存在响应处理
            $rule = Db::name('mp_rule')
                ->where(['mpid' => $this->mid, 'event' => 'unidentified', 'status' => '1'])->find();
            if (!empty($rule)) {
                if ($rule['keyword']) {
                    $this->keyword($rule['keyword'], $msgData);
                }
                if ($rule['addon']) {
                    loadAdApi($rule['addon'], $msgData,['mid'=>$this->mid,'addon'=>$rule['addon']]);
                }
            }else{
                $this->mpMsg($msgData);
            }

        }
    }

    /**
     * 扫码关注/扫码事件
     * @author geeson 314835050@qq.com
     * @param $result array 场景二维码数组
     * @param $msgData array 微信服务器发来数组
     */
    public function qrcode($result = [], $msgData = [])
    {
        $data = [];
        if ($result = Qrcode::get(['ticket' => $msgData['Ticket']])) {
            Qrcode::where(['ticket' => $msgData['Ticket']])
                ->setInc('scan_count');
            $data['scene_id'] = $result['scene_id'];
            $data['openid'] = $msgData['FromUserName'];
            $data['create_time'] = time();
            $data['mpid'] = $result['mpid'];
            $data['qrcode_id'] = $result['id'];
            if (Db::name('qrcode_data')->where(['scene_id' => $data['scene_id'], 'openid' => $data['openid']])->find()) {
                Db::name('qrcode_data')
                    ->where(['scene_id' => $data['scene_id'], 'openid' => $data['openid']])
                    ->setInc('scan_count');
            } else {
                $data['type'] = '1';
                Db::name('qrcode_data')->insert($data);
            }
        }
        $Msg = array_merge($msgData, $data);
        $this->keyword($result['keyword'], $Msg);
    }

    /**
     * 关键词回复规则
     * @param $keyword 消息文本
     * @author geeson myrhzq@qq.com
     * @param $msg array 微信消息
     * case 回复类型 text,addon,images,news,voice,music,video
     *
     */
    public function keyword($keyword, $msg = [])
    {
        $rule = Db::name('mp_rule')->where(['mpid' => $this->mid, 'keyword' => $keyword, 'status' => '1'])
            ->where('event', 'null')
            ->order('id Desc')->find();
        if (!empty($rule)) {
            switch ($rule['type']) {//text,addon,images,news,voice,music,video
                case 'addon'://该关键词是插件应用响应的
                    loadAdApi($rule['addon'], $msg,['mid'=>$this->mid,'addon'=>$rule['addon']]);
                    break;
                case 'text'://文本
                    $content = Db::name('mp_reply')->where(['reply_id' => $rule['reply_id']])->field('content')->find();
                    replyText($content['content']);
                    break;
                case 'image'://图片
                    $result = Db::name('mp_reply')->where(['reply_id' => $rule['reply_id']])->find();
                    if (!empty($result)) {
                        replyImage($result['media_id']);
                    }
                    break;
                case 'news'://图文
                    $content = Db::name('mp_reply')->where(['reply_id' => $rule['reply_id']])->select();
                    $news = [];
                    foreach ($content as $key1 => $v) {
                        foreach ($v as $key2 => $v) {
                            if ($key2 == 'title') {
                                $news[$key1]['Title'] = $v;
                            }
                            if ($key2 == 'content') {
                                $news[$key1]['Description'] = $v;
                            }
                            if ($key2 == 'url') {
                                $news[$key1]['PicUrl'] = $v;
                            }
                            if ($key2 == 'link') {
                                $news[$key1]['Url'] = $v. '?openid=' . getOrSetOpenid();
                            }
                        }
                    }
                    replyNews($news);
                    break;
                case 'voice'://语音
                    $result = Db::name('mp_reply')->where(['reply_id' => $rule['reply_id']])->find();
                    if (!empty($result)) {
                        replyVoice($result['media_id']);
                    }
                    break;
                case 'music'://音乐
                    $result = Db::name('mp_reply')->where(['reply_id' => $rule['reply_id']])->find();
                    if (is_array($result)) {
                        replyMusic($result['title'], $result['content'], $result['url'], $result['link']);
                    }
                    break;
                case 'video'://视频
                    $result = Db::name('mp_reply')->where(['reply_id' => $rule['reply_id']])->find();
                    if (!empty($result)) {
                        replyVideo($result['media_id'], $result['title'], $result['content']);
                    }
                    break;
                case 'member'://会员登录
                    $content = Db::name('mp_reply')->where(['reply_id' => $rule['reply_id']])->select();
                    $news = [];
                    foreach ($content as $key1 => $v) {
                        foreach ($v as $key2 => $v) {
                            if ($key2 == 'title') {
                                $news[$key1]['Title'] = '会员登录';
                            }
                            if ($key2 == 'url') {
                                $news[$key1]['PicUrl'] = $v;
                            }
                            if ($key2 == 'link') {
                                $news[$key1]['Url'] = $v . '?openid=' . getOrSetOpenid();
                            }
                        }
                    }
                    replyNews($news);
                    break;
                default:
                    //
                    break;
            }
        } else {
            $rule = Db::name('mp_rule')
                ->where(['mpid' => $this->mid, 'event' => 'unidentified', 'status' => '1'])->find();
            if (!empty($rule)) {
                if ($rule['keyword']) {
                    $this->keyword($rule['keyword'], $msg);
                }
                if ($rule['addon']) {
                    loadAdApi($rule['addon'], $msg,['mid'=>$this->mid,'addon'=>$rule['addon']]);
                }
            }else{
                $this->mpMsg($msg);
            }

        }

    }

    /**
     * 粉丝关注\取关
     * @author myrhzq@qq.com
     */

    public function subscribe($msg = [], $type = 'subscribe')
    {
        $friendInfo = getFriendInfoForApi(getOrSetOpenid());
        $friendInfo['tagid_list']=json_encode($friendInfo['tagid_list']);
        $friendModel = new MpFriends();
        if (!empty($friendInfo)) {
            $friendInfo['mpid'] = $this->mid;
            if ($type == 'subscribe') {
                $Res = $friendModel->where(['mpid' => $this->mid, 'openid' => getOrSetOpenid()])->find();
                if (empty($Res)) {
                    $friendModel->save($friendInfo);
                } else {
                    $friendModel->save($friendInfo,['mpid' => $this->mid, 'openid' => getOrSetOpenid()]);
                }
            } elseif ($type == 'unsubscribe') {
                $friendModel->save(['subscribe' => '0', 'unsubscribe_time' => time()],['mpid' => $this->mid, 'openid' => getOrSetOpenid()]);
            }
        }else{//公众号没有权限获取用户基本信息 可按需求扩展
            if ($type == 'subscribe') {

            } elseif ($type == 'unsubscribe') {

            }
        }
        $rule = Db::name('mp_rule')->where(['mpid' => $this->mid, 'event' => 'subscribe'])->find();
        if (!empty($rule)) {
            if ($rule['keyword']) {
                $this->keyword($rule['keyword'], $msg);
            }
            if ($rule['addon']) {
                loadAdApi($rule['addon'], $msg,['mid'=>$this->mid,'addon'=>$rule['addon']]);
            }
        }


    }
}