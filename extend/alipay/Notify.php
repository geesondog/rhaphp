<?php

namespace alipay;

use think\Loader;

Loader::import('alipay.pay.service.AlipayTradeService');

/**
* 支付回调处理类
*
* 用法建议:
* 1.$_POST获取参数
* 2.调用 \alipay\Notify::checkSign($params) 进行签名校检
* 3.调用 \alipay\Notify::checkParams($orginParams, $orginParams) 进行参数验证
* 4.根据 $_POST['trade_status'] 判断订单状态
* 5.echo "success"; 或者 echo "fail";
*
* ----------------- 求职 ------------------
* 姓名: zhangchaojie      邮箱: zhangchaojie_php@qq.com  应届生
* 期望职位: PHP初级工程师 薪资: 3500  地点: 深圳(其他城市亦可)
* 能力:
*     1.熟悉小程序开发, 前后端皆可, 前端一日可做5-10个页面, 后端可写接口
*     2.后端, PHP基础知识扎实, 熟悉ThinkPHP5框架, 用TP5做过CMS, 商城, API接口
*     3.MySQL, Linux都在进行进一步学习
*
* 如有大神收留, 请发送邮件告知, 必将感激涕零!
*/
class Notify
{
    /**
     * 检查签名
     */
    public static function checkSign($params)
    {
        $config = config('alipay');
        $alipaySevice = new \AlipayTradeService($config);
        $result = $alipaySevice->check($params);
        return $result;
    }

    /**
     * 判断两个数组是否一致, 两个数组的参数可以为如下（建议）：
     * $params['out_trade_no'] 商户单号
     * $params['total_amount'] 订单金额
     * $params['app_id']       app_id号
     */
    public static function checkParams($orginParams, $orginParams)
    {
        $result = array_diff($orginParams, $orginParams);
        if (empty($result)) {
            return true;
        } else {
            return false;
        }
    }
}