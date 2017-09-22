<?php
namespace addons\redPack\controller;


use addons\redPack\model\Redpack;
use think\Db;

class Api
{

    public function message($msg = [], $param = [])
    {
        $info = getAddonInfo();
        if (time() < strtotime($info['mp_config']['start_time'])) {
            replyText('红包活动还没有开始');
            exit;
        }
        if (time() > strtotime($info['mp_config']['end_time'])) {
            replyText('红包活动已经结束');
            exit;
        }
        $model = new Redpack();
        $total = $model->where(['mpid' => $param['mid'], 'openid' => $msg['FromUserName'],'status'=>1])->sum('money');
        if ($total >= $info['mp_config']['amount']) {
            replyText('红包已经被领完');
            exit;
        }
        $redPackCount = $model->where(['mpid' => $param['mid'], 'openid' => $msg['FromUserName'],'status'=>1])->count('id');
        if ($redPackCount >= $info['mp_config']['number_of_times']) {
            replyText('你已经领取过红包');
            exit;
        } else {
            $data = [
                'openid' => $msg['FromUserName'],
                'nick_name' => $info['mp_config']['nick_name'],
                'send_name' => $info['mp_config']['send_name'],
                'money' => $info['mp_config']['money'],
                'wishing' => $info['mp_config']['wishing'],
                'act_name' => $info['mp_config']['act_name'],
            ];
            $result = sendRedpack($param['mid'], $data, $param['addon']);
            if ($result['errCode'] == 0) {

                replyText($info['mp_config']['reply_msg']);
            } else {
                replyText($result['errMsg']);
            }
        }
    }

}