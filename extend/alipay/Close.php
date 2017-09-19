<?php

namespace alipay;

use think\Loader;

Loader::import('alipay.pay.service.AlipayTradeService');
loader::import('alipay.pay.buildermodel.AlipayTradeCloseContentBuilder');

/**
* 统一收单交易关闭接口
*
* 用法:
* 调用 \alipay\Close::exec($query_no) 即可
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
class Close
{
    // 商户订单号(out_trade_no) or 支付宝交易号(trade_no) 二选一
    const QUERY_TYPE = 'trade_no';

    public static function exec($query_no)
    {
        // 1.构建请求参数
        $RequestBuilder = new \AlipayTradeCloseContentBuilder();
        if (self::QUERY_TYPE == 'trade_no') {
            $RequestBuilder->setTradeNo(trim($query_no));
        } else {
            $RequestBuilder->setOutTradeNo(trim($query_no));
        }

        // 2.获取配置
        $config = config('alipay');
        $aop    = new \AlipayTradeService($config);

        // 3.发起请求
        $response = $aop->Close($RequestBuilder);

        // 4.转为数组格式返回
        $response = json_decode(json_encode($response), true);

        // 5.进行结果处理
        if (!empty($response['code']) && $response['code'] != '10000') {
            self::processError('交易关闭接口出错, 错误码: '.$response['code'].' 错误原因: '.$response['sub_msg']);
        }

        return $response;
    }

    /**
     * 统一错误处理接口
     * @param  string $msg 错误描述
     */
    private static function processError($msg)
    {
        throw new \think\Exception($msg);
    }
}