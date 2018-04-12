<?php
// +----------------------------------------------------------------------
// | [RhaPHP System] Copyright (c) 2017 http://www.rhaphp.com/
// +----------------------------------------------------------------------
// | [RhaPHP] 并不是自由软件,你可免费使用,未经许可不能去掉RhaPHP相关版权
// +----------------------------------------------------------------------
// | Author: Geeson <qimengkeji@vip.qq.com>
// +----------------------------------------------------------------------


namespace app\mp\controller;


use app\common\model\MemberWealthRecord;
use app\common\model\MpFriends;
use app\common\model\Setting;
use think\facade\Cache;
use think\Controller;
use think\facade\Cookie;
use think\facade\Request;
use think\facade\Session;

class Login extends Controller
{


    public function registerByPhone($mid)
    {
        $registerConfValue = getSetting($mid, 'register');
        $this->assign('conf', $registerConfValue);
        return view('login');
    }

    /**
     * @author Geeson 314835050@qq.com
     * @param $mid
     * @return \think\response\View
     */
    public function setPassword($mid)
    {
        if (session('isNews') != '1') {
            $this->assign('errMsg', '请返回公众号重新注册');
            return view('common/error');
        }
        if (Request::isAjax()) {
            $data = input();
            if (empty($data['password']) || $data['password'] != $data['repassword']) {
                ajaxMsg(0, '密码为空，或者密码不匹配');
            } else {
                $model = new MpFriends();
                if ($model->save(['password' => md5($data['password']), 'type' => '1'], ['mpid' => $mid, 'openid' => getOrSetOpenid()])) {
                    $setingModel = new Setting();
                    $registerConfValue = $setingModel->getSetting(['mpid' => $mid, 'name' => 'register','cate'=>'mp']);
                    session('isNews', '');
                    $member = getMemberInfo();
                    session('member_' . $mid, $member);
                    Cookie::forever('member_' . $mid, $member);
                    ajaxReturn(['url' => $registerConfValue['redirect_url'] . '?openid=' . getOrSetOpenid()], 1, '注册成功');
                } else {
                    ajaxMsg(0, '注册失败');
                }
            }
        }
        return view('setpassword');
    }

    /**
     * @author Geeson 314835050@qq.com
     * @return \think\response\View
     */
    public function loginByReply()
    {

        $get['openid'] = input('openid');
        getOrSetOpenid($get['openid']);
        $get['mid'] = input('mid');
        $model = new MpFriends();
        $setingModel = new Setting();
        $registerConfValue = $setingModel->getSetting(['mpid' => $get['mid'], 'name' => 'register','cate'=>'mp']);
        if (isset($registerConfValue['register_type']) && $registerConfValue['register_type'] == 2) {
            $this->error('此登录或者注册方式已经关闭');
        }

        if (Request::isAjax()) {
            $data = input();
            if (empty($data['password'])) {
                ajaxMsg(0, '密码不能为空');
            } else {
                if ($memberInfo = $model->where(['mpid' => $get['mid'], 'openid' => getOrSetOpenid(), 'password' => md5($data['password'])])->find()) {
                    $member = getMemberInfo();
                    session('member_' . $get['mid'], $member);
                    Cookie::forever('member_' . $get['mid'], $member);
                    ajaxReturn(['url' => $registerConfValue['redirect_url'] . '?openid=' . getOrSetOpenid()], 1, '登录成功');
                } else {
                    ajaxMsg(0, '密码错误');
                }
            }
        }
        if ($get['mid'] == '') {
            $this->assign('errMsg', 'Mid不存在');
            return view('common/error');
        }
        if ($get['openid'] == '') {
            $this->assign('errMsg', 'Openid不存在<br>或者你需要在公众菜单或者回复进入');
            return view('common/error');
        }
        if (!$model->getMemberInfo(['openid' => $get['openid']])) {
            $_friend['openid'] = $get['openid'];
            $_friend['nickname'] = '新注册会员';
            $_friend['type'] = '1';
            $_friend['mpid'] = $get['mid'];
            $model->save($_friend);
            $member = getMemberInfo();
            if ($registerConfValue) {
                $MemberModel = new MemberWealthRecord();
                if ($registerConfValue['up_score'] > 0) {
                    $MemberModel->addScore($member['id'], $get['mid'], $registerConfValue['up_score'], '注册会员福利');
                }
                if ($registerConfValue['up_money'] > 0) {
                    $MemberModel->addMoney($member['id'], $get['mid'], $registerConfValue['up_money'], '注册会员福利');
                }
            }
        }
        $member = getMemberInfo();
        if ($registerConfValue['ispwd'] == 0) {//不需要密码登录 跳转
            //TODO
            session('member_' . $get['mid'], $member);
            Cookie::forever('member_' . $get['mid'], $member);
            $this->redirect($registerConfValue['redirect_url'] . '?openid=' . getOrSetOpenid());
        } else {
            if ($member['password'] == '') {
                session('isNews', '1');
                $this->redirect('Login/setPassword', ['mid' => $get['mid']]);
            } else {
                return view('login');
            }
        }
    }

    public function registerByAuto($mid, $scope)
    {

        $callbackUrl = getHostDomain() . url('mp/Login/autoLogin', ['mid' => $mid]);
        $weOBJ = getWechatActiveObj(input('mid'));
        $url = urldecode($weOBJ->getOauthRedirect($callbackUrl, 'state', $scope));
        $this->redirect($url);
    }

    /**
     * @author Geeson 314835050@qq.com
     * @param $mid
     */
    public function autoLogin($mid)
    {
        $weOBJ = getWechatActiveObj(input('mid'));
        if (!$data = $weOBJ->getOauthAccessToken()) {
            if($msg=wxApiResultErrorCode($weOBJ->errCode)){
                exit('获取accessToken失败 errcode:' . $msg);
            }
            exit('获取accessToken失败 errcode:' . $weOBJ->errCode . ' errmsg:' . $weOBJ->errMsg);
        } else {
            $userInfo = $weOBJ->getOauthUserinfo($data['access_token'], $data['openid']);
            getOrSetOpenid($data['openid']);
            $model = new MpFriends();
            $isNewUser = false;
            if (!$_member=$model->getMemberInfo(['openid' => $data['openid']])) {
                $isNewUser = true;
                $userInfo['openid']=$data['openid'];
                if ($model->register($userInfo, $mid)) {
                    if ($isNewUser == true) {
                        $setingModel = new Setting();
                        $registerConfValue = $setingModel->getSetting(['mpid' => $mid, 'name' => 'register','cate'=>'mp']);
                        if ($registerConfValue) {
                            $member = getMemberInfo();
                            if ($registerConfValue['up_score'] > 0) {
                                $MemberModel = new MemberWealthRecord();
                                $MemberModel->addScore($member['id'], $mid, $registerConfValue['up_score'], '注册会员福利');
                            }
                            if ($registerConfValue['up_money'] > 0) {
                                $MemberModel = new MemberWealthRecord();
                                $MemberModel->addMoney($member['id'], $mid, $registerConfValue['up_money'], '注册会员福利');
                            }
                        }
                    }
                }
            }
            if($_member=$model->getMemberInfo(['openid' => $data['openid']])){
                $member = getMember($_member['id']);
                session('member_' . $mid, $member);
                Cookie::forever('member_' . $mid, $member);
                $this->redirect(session('callbackUrl'));
            }
        }
    }

    public function lout($mid)
    {
        Session::clear('member_' . $mid);
        Cookie::clear('member_' . $mid);
        ajaxMsg(1, '退出成功！请关闭页面');
    }

}