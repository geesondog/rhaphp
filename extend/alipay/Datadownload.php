<?php

namespace alipay;

use think\Loader;

Loader::import('alipay.pay.service.AlipayTradeService');
loader::import('alipay.pay.buildermodel.AlipayDataDataserviceBillDownloadurlQueryContentBuilder');

/**
* 查询账单下载地址接口
*
* 用法:
* 调用 \alipay\Datadownload::exec($bill_type, $bill_date) 即可
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
class Datadownload
{
    /**
     * 主入口
     * @param  string $bill_type trade/signcustomer, trade指商户基于支付宝交易收单的业务账单；signcustomer是指基于商户支付宝余额收入及支出等资金变动的帐务账单；
     * @param  string $bill_date 日期, 单格式为yyyy-MM-dd，月账单格式为yyyy-MM
     * 注意：
     * 1.当日或者当月日期为无效日期，毕竟没有过当日或者当月
     * 2.如果是2017年7月，请填写2017-07，不能为2017-7
     */
    public static function exec($bill_type, $bill_date)
    {
        // 1.校检参数
        self::checkParams($bill_type, $bill_date);

        // 2.设置请求参数
        $RequestBuilder = new \AlipayDataDataserviceBillDownloadurlQueryContentBuilder();
        $RequestBuilder->setBillType($bill_type);
        $RequestBuilder->setBillDate($bill_date);

        // 3.获取配置
        $config = config('alipay');
        $Response = new \AlipayTradeService($config);

        // 4.请求
        $response = $Response->downloadurlQuery($RequestBuilder);

        // 5.转为数组格式返回
        $response = json_decode(json_encode($response), true);

        // 6.进行结果处理
        if (!empty($response['code']) && $response['code'] != '10000') {
            self::processError('查询账单接口出错, 错误码: '.$response['code'].' 错误原因: '.$response['sub_msg']);
        }

        return $response;
    }

    /**
     * 校检参数
     */
    private static function checkParams($bill_type, $bill_date)
    {
        if (!in_array($bill_type, ['trade', 'signcustomer'])) {
            self::processError('账单类型不正确');
        }

        if (!strtotime($bill_date)) {
            self::processError('日期格式不正确');
        }
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