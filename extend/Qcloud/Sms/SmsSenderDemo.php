<?php

namespace Qcloud\Sms\Demo;

require_once "SmsSender.php";
require_once "SmsVoiceSender.php";

use Qcloud\Sms\SmsSingleSender;
use Qcloud\Sms\SmsMultiSender;
use Qcloud\Sms\SmsVoicePromtSender;
use Qcloud\Sms\SmsVoiceVeriryCodeSender;

try {
    // 请根据实际 appid 和 appkey 进行开发，以下只作为演示 sdk 使用
    // appid,appkey,templId申请方式可参考接入指南 https://www.qcloud.com/document/product/382/3785#5-.E7.9F.AD.E4.BF.A1.E5.86.85.E5.AE.B9.E9.85.8D.E7.BD.AE
    $appid = 122333333;
    $appkey = "111111111112132312xx";
    $phoneNumber1 = "21212313123";
    $phoneNumber2 = "12345678902";    
    $phoneNumber3 = "12345678903";
    $templId = 7839;


    $singleSender = new SmsSingleSender($appid, $appkey);

    // 普通单发
    $result = $singleSender->send(0, "86", $phoneNumber2, "测试短信，普通单发，深圳，小明，上学。", "", "");
    $rsp = json_decode($result);
    echo $result;
    echo "<br>";


    // 指定模板单发
    // 假设模板内容为：测试短信，{1}，{2}，{3}，上学。
    $params = array("指定模板单发", "深圳", "小明");
    $result = $singleSender->sendWithParam("86", $phoneNumber2, $templId, $params, "", "", "");
    $rsp = json_decode($result);
    echo $result;
    echo "<br>";

    $multiSender = new SmsMultiSender($appid, $appkey);

    // 普通群发
    $phoneNumbers = array($phoneNumber1, $phoneNumber2, $phoneNumber3);
    $result = $multiSender->send(0, "86", $phoneNumbers, "测试短信，普通群发，深圳，小明，上学。", "", "");
    $rsp = json_decode($result);
    echo $result;
    echo "<br>";

    // 指定模板群发，模板参数沿用上文的模板 id 和 $params
    $params = array("指定模板群发", "深圳", "小明");
    $result = $multiSender->sendWithParam("86", $phoneNumbers, $templId, $params, "", "", "");
    $rsp = json_decode($result);
    echo $result;
    echo "<br>";

    //语音验证码发送
    $voiceVeriyCodeSender = new SmsVoiceVerifyCodeSender($appid,$appkey);
    $result = $voiceVeriyCodeSender->send("86",$phoneNumber1,"1234",2,"");
    $rsp = json_decode($result);
    echo $result;
    echo "<br>";

    //语音通知发送
    $voiceVeriyCodeSender = new SmsVoicePromptSender($appid,$appkey);
    $result = $voiceVeriyCodeSender->send("86",$phoneNumber1,"1234",2,"");
    $rsp = json_decode($result);
    echo $result;
    echo "<br>";

} catch (\Exception $e) {
    echo var_dump($e);
}
