<?php

namespace alipay;

use think\Loader;

Loader::import('alipay.pay.service.AlipayTradeService');
loader::import('alipay.pay.buildermodel.AlipayTradeFastpayRefundQueryContentBuilder');

/**
* 退款统一订单查询
*
* 用法:
* 调用 \alipay\RefundQuery::exec($params) 即可
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
class RefundQuery
{
    /**
     * 主入口
     * @param  array $params 退款查询参数, 具体如下:
     * @param string $params['trade_no']/$params['out_trade_no'] 商户订单号或支付宝单号其中之一
     * @param string $params['out_request_no'] 可空, 为空时, 退款号为订单号
     */
    public static function exec($params)
    {
        // 1.校检参数
        if (empty($params['trade_no']) && empty($params['out_trade_no'])) {
            throw new \think\Exception('商户订单号(out_trade_no)和支付宝单号(trade_no)不得通知为空');
        }

        // 2.构造请求参数
        $RequestBuilder = self::builderParams($params);

        // 3.获取配置
        $config = config('alipay');
        $aop    = new \AlipayTradeService($config);

        // 4.发起请求
        $response = $aop->refundQuery($RequestBuilder);

        // 5.转为数组格式返回
        $response = json_decode(json_encode($response), true);

        // 6.进行结果处理
        if (!empty($response['code']) && $response['code'] != '10000') {
            self::processError('退款查询接口出错, 错误码为: '.$response['code'].', 错误原因为: '.$response['sub_msg']);
        }

        return $response;
    }

    /**
     * 构造请求参数
     */
    private static function builderParams($params)
    {
        $RequestBuilder = new \AlipayTradeFastpayRefundQueryContentBuilder();
        if (isset($params['trade_no'])) {
            $RequestBuilder->setTradeNo($params['trade_no']);
        } else {
            $RequestBuilder->setOutTradeNo($params['out_trade_no']);
        }

        // 如果未传退款号, 则以单号为退款号查询
        if (isset($params['out_request_no'])) {
            $RequestBuilder->setOutRequestNo($params['out_request_no']);
        } else {
            $out_request_no = isset($params['trade_no']) ? $params['trade_no'] : $params['out_trade_no'];
            $RequestBuilder->setOutRequestNo($out_request_no);
        }

        return $RequestBuilder;
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