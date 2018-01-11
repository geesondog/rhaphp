<?php

namespace addons\chongZhi\controller;


use app\common\controller\Addon;
use app\common\model\Payment;
use think\facade\Request;

class Deposit extends Addon
{
    public $isWexinLogin = true;


    public function index()
    {
        $this->assign('member', getMember());
        $this->fetch();
    }

    public function topUp()
    {

        if (Request::isPost()) {
            if ($member = getMember()) {
                $money = input('post.money');
                if (isset($member['openid']) && !empty($member['openid'])) {
                    if (empty($money) || $money < 0.09) {
                        ajaxMsg(0, '金额最小为0.1元');
                    } else {
                        $mid = $this->mid;
                        if (!$mid && $mid != $member['mpid']) {
                            ajaxMsg(0, '公众号标识与当前用户不匹配');
                        }
                        $model = new Payment();
                        if ($id = $model->addPayment($member['id'], $member['mpid'], $money, '账户充值')) {
                            ajaxReturn(['url' => getWxPayUrl($this->mid,['payment_id' => $id,'view'=>$this->addonRoot.'/view/common/pay.html'])]);
                        } else {
                            ajaxMsg(0, '下单失败');
                        }
                    }
                } else {
                    ajaxMsg(0, '支付参数：openid不存在');
                }
            } else {
                ajaxMsg(0, '用户不存在');
            }
        } else {
            $this->assign('title', '账户充值');
            $this->fetch();
        }
    }

    /*
     * 订单
     */
    public function queryOrder()
    {
        $member = getMember();
        $model = new Payment();
        $orders = $model->where(['mpid' => input('mid'), 'member_id' => $member['id']])->order('create_time DESC')->paginate(10);
        $this->assign('orders', $orders);
        $this->assign('title', '我的订单');
        $this->fetch();
    }

}