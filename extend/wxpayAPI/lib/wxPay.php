<?php
/**
 * 微信支付企业付款接口
 */
//    ini_set('display_errors', 0);
//    header("Content-type:text/html;chartset=utf-8");
//    $wxPay = new wxPay();
//    $wxPay->pay('oc3p8ty26j417cDO6VQ0qVDN0hwo', '李楚彪', '测试4', 100);
class wxPay
{
    //=======【证书路径设置】=====================================
    //证书路径,注意应该填写绝对路径
    protected $SSLCERT_PATH = '../cert/apiclient_cert.pem';
    protected $SSLKEY_PATH = '../cert/apiclient_key.pem';
    //=======【基本信息设置】=====================================
    //微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看
    protected $APPID = 'wxa32c969fa0fc81b7';
    //受理商ID，身份标识
    protected $MCHID = '1423340802';
    //商户支付密钥Key。审核通过后，在微信发送的邮件中查看
    protected $KEY = 'duola90da6f1265e4901ffb80afaa36f';
    //JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
    protected $APPSECRET = '759cd9ed5683c7dbc06a8ab5e95086db';
    //JSAPI接口地址
    protected $APPURL = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';

    /**
     * 微信企业付款
     * @param  [type] $openid   商户appid下，某用户的openid
     * @param  [type] $username 收款用户真实姓名。
     * @param  [type] $desc     企业付款操作说明信息。必填。
     * @param  [type] $money    企业付款金额，单位为分
     * @author saso <--415944661@qq.com-->
     */
    function pay($openid = '', $username = '', $desc = '', $money = 0)
    {
        $apiUrl = $this->APPURL;//企业付款接口url
        $Parameters = array();
        $Parameters['amount'] = $money;//企业付款金额，单位为分
        $Parameters['check_name'] = 'OPTION_CHECK';//NO_CHECK：不校验真实姓名 FORCE_CHECK：强校验真实姓名（未实名认证的用户会校验失败，无法转账） OPTION_CHECK：针对已实名认证的用户才校验真实姓名（未实名认证用户不校验，可以转账成功）
        $Parameters['desc'] = $desc;//企业付款操作说明信息。必填。
        $Parameters['mch_appid'] = $this->APPID;//微信分配的公众账号ID
        $Parameters['mchid'] = $this->MCHID;//微信支付分配的商户号
        $Parameters['nonce_str'] = $this->createNoncestr();//随机字符串，不长于32位
        $Parameters['openid'] = $openid;//商户appid下，某用户的openid
        $Parameters['partner_trade_no'] = 'saso' . time() . rand(10000, 99999);//商户订单号，需保持唯一性
        $Parameters['re_user_name'] = $username;//收款用户真实姓名。 如果check_name设置为FORCE_CHECK或OPTION_CHECK，则必填用户真实姓名
        $Parameters['spbill_create_ip'] = $_SERVER['SERVER_ADDR'];//调用接口的机器Ip地址
        $Parameters['sign'] = $this->getSign($Parameters);//签名
        $xml = $this->arrayToXml($Parameters);
        $res = $this->postXmlSSLCurl($xml, $apiUrl);
        $return = $this->xmlToArray($res);
       // var_dump($return);
        return $return;
    }

    /**
     *  作用：格式化参数，签名过程需要使用
     */
    function formatBizQueryParaMap($paraMap, $urlencode)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
            if ($urlencode) {
                $v = urlencode($v);
            }
            //$buff .= strtolower($k) . "=" . $v . "&";
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar = '';
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }

    /**
     *  作用：生成签名
     */
    function getSign($Obj)
    {
        foreach ($Obj as $k => $v) {
            $Parameters[$k] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);
        //echo '【string1】'.$String.'</br>';
        //签名步骤二：在string后加入KEY
        $String = $String . "&key=849829de4e8a32eca903dc826cf52ea9";
        //echo "【string2】".$String."</br>";
        //签名步骤三：MD5加密
        $String = md5($String);
        //echo "【string3】 ".$String."</br>";
        //签名步骤四：所有字符转为大写
        $result_ = strtoupper($String);
        //echo "【result】 ".$result_."</br>";
        return $result_;
    }

    /**
     *  作用：产生随机字符串，不长于32位
     */
    function createNoncestr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     *  作用：array转xml
     */
    function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";

            } else
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     *  作用：将xml转为array
     */
    function xmlToArray($xml)
    {
        //将XML转为array        
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }

    /**
     *  作用：使用证书，以post方式提交xml到对应的接口url
     */
    function postXmlSSLCurl($xml, $url, $second = 30)
    {
        $ch = curl_init();
        //超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //设置证书
        //使用证书：cert 与 key 分别属于两个.pem文件
        //默认格式为PEM，可以注释
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLCERT, $this->SSLCERT_PATH);
        //默认格式为PEM，可以注释
        curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLKEY, $this->SSLKEY_PATH);
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $data = curl_exec($ch);
        //返回结果
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            echo "curl出错，错误码:$error" . "<br>";
            curl_close($ch);
            return false;
        }
    }
}