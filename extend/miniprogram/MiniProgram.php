<?php
// +----------------------------------------------------------------------
// | [RhaPHP System] Copyright (c) 2017 http://www.rhaphp.com/
// +----------------------------------------------------------------------
// | [RhaPHP] 并不是自由软件,你可免费使用,未经许可不能去掉RhaPHP相关版权
// +----------------------------------------------------------------------
// | Author: Geeson <qimengkeji@vip.qq.com>
// +----------------------------------------------------------------------

namespace miniprogram;
class MiniProgram
{
    private $appid;
    private $appsecret;
    private $token;
    private $encodingaeskey;
    public $errcode = 0;
    public $errmsg = '';
    private $access_token;
    private $encrypt_type;
    private $postxml;
    private $_receive;
    const API_BASE_URL = 'https://api.weixin.qq.com/';

    public function __construct($option)
    {
        $this->appid = isset($option['appid']) ? $option['appid'] : '';
        $this->appsecret = isset($option['appsecret']) ? $option['appsecret'] : '';
        $this->token = isset($option['token']) ? $option['token'] : '';
        $this->encodingaeskey = isset($option['encodingaeskey']) ? $option['encodingaeskey'] : '';
    }

    /**
     * 获取错误代码
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errcode;
    }

    /**
     * 获取错误内容
     * @return string
     */
    public function getErrorMsg()
    {
        return $this->errmsg;
    }

    /**
     * 获取ACCESS_TOKEN
     * @return bool|mixed
     */
    public function getAccessToken()
    {
        $cahceName = $this->appid . 'access_token';
        if ($this->access_token = self::getCache($cahceName)) {
            return $this->access_token;
        } else {
            $url = self::API_BASE_URL . 'cgi-bin/token?grant_type=client_credential&appid=' . $this->appid . '&secret=' . $this->appsecret . '';
            $result = self::checkErrorCode(json_decode(self::httpGet($url), true));
            if ($result) {
                self::setCache($cahceName, $result['access_token'], $result['expires_in']);
                return $this->access_token = $result['access_token'];
            } else {
                return false;
            }
        }

    }

    /**
     * 获取微信发来消息初始化方法（获取消息必须首调）
     * @return bool
     */
    public function init()
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            $postStr = file_get_contents("php://input");
            $array = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $this->encrypt_type = isset($_GET["encrypt_type"]) ? $_GET["encrypt_type"] : '';
            if ($this->encrypt_type == 'aes') { //aes加密
                $encryptStr = $array['Encrypt'];
                $pc = new Prpcrypt($this->encodingaeskey);
                $array = $pc->decrypt($encryptStr, $this->appid);
                if (is_array($array) && isset($array['errcode'])) {
                    $this->errcode = $array['errcode'];
                    $this->errmsg = $array['errmsg'];
                    return false;
                }
                $this->postxml = $array;
            } else {
                $this->postxml = $postStr;
            }
            return true;
        } elseif (isset($_GET["echostr"])) {
            if ($this->checkSignature()) {
                return 'success';
            } else {
                $this->errcode = Error::$checkSignaturError;
                $this->errmsg = Error::$checkSignaturErrorMsg;
                return false;
            }
        }
    }

    /**
     * 检验signature
     * @return bool
     */
    public function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = $this->token;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取微信发来信息数据
     * @return array
     */
    public function getRev()
    {
        if ($this->_receive) return $this->_receive;
        return $this->_receive = (array)simplexml_load_string($this->postxml, 'SimpleXMLElement', LIBXML_NOCDATA);

    }

    /**
     * $data = ['touser' => $openid,'msgtype' => 'text','text' => ['content' => $msg_content]];
     * 向微信发送客服消息
     * @param array $data
     * @return bool
     */
    public function sendCustomMessage($data = [])
    {
        if (!$this->getAccessToken()) return false;
        $url = self::API_BASE_URL . 'cgi-bin/message/custom/send?access_token=' . $this->access_token;
        return self::checkErrorCode(json_decode(self::httpPost($url, json_encode($data,JSON_UNESCAPED_UNICODE)), true));
    }

    /**
     * ['media'=>'@'.$file]
     * 新增临时素材
     * 小程序可以使用本接口把媒体文件（目前仅支持图片）上传到微信服务器，用户发送客服消息或被动回复用户消息。
     * @param $data
     * @param string $type
     * @return bool
     */
    public function uploadMedia($data, $type = 'image')
    {
        if (!$this->getAccessToken()) return false;
        $url = self::API_BASE_URL . 'cgi-bin/media/upload?access_token=' . $this->access_token . '&type=' . $type;
        return self::checkErrorCode(json_decode(self::httpPost($url, json_encode($data), true), true));
    }

    /**
     * 获取分析数据【概况趋势】
     * 日期为空，默认是昨天的数据
     * @param string $begin_date 开始日期 如：20180108
     * @param string $end_date 结束日期 如：20180108 ，限定查询1天数据，end_date允许设置的最大值为昨日
     * @return array
     */
    public function getSummaryTrend($begin_date = '', $end_date = '')
    {
        $url = self::API_BASE_URL . 'datacube/getweanalysisappiddailysummarytrend?access_token=';
        return $this->analysis($url, $begin_date, $end_date);

    }

    /**
     * 访问分析【日趋势】
     * @param string $begin_date 开始日期 如：20180108
     * @param string $end_date 结束日期 如：20180108
     * @return array
     */
    public function getVisittrendTrendByDay($begin_date = '', $end_date = '')
    {
        $url = self::API_BASE_URL . 'datacube/getweanalysisappiddailyvisittrend?access_token=';
        return $this->analysis($url, $begin_date, $end_date);

    }

    /**
     * 访问分析【周趋势】(日期范围为周)默认上周
     * @param string $begin_date 开始日期 如：20180226
     * @param string $end_date 结束日期 如：20180304
     * @return array
     */
    public function getVisittrendTrendByweek($begin_date = '', $end_date = '')
    {
        $begin_date = $begin_date ? $begin_date : date("Ymd", mktime(0, 0, 0, date("m"), date("d") - date("w") + 1 - 7, date("Y")));
        $end_date = $end_date ? $end_date : date("Ymd", mktime(23, 59, 59, date("m"), date("d") - date("w") + 7 - 7, date("Y")));
        $url = self::API_BASE_URL . 'datacube/getweanalysisappidweeklyvisittrend?access_token=';
        return $this->analysis($url, $begin_date, $end_date);

    }

    /**
     * 访问分析【月趋势】(日期范围为周)默认上个月
     * @param string $begin_date 开始日期 如：20180201
     * @param string $end_date 结束日期 如：20180228
     * @return array
     */
    public function getVisittrendTrendByMonth($begin_date = '', $end_date = '')
    {
        $begin_date = $begin_date ? $begin_date : date("Ymd", mktime(0, 0, 0, date("m") - 1, 1, date("Y")));
        $end_date = $end_date ? $end_date : date("Ymd", mktime(23, 59, 59, date("m"), 0, date("Y")));
        $url = self::API_BASE_URL . 'datacube/getweanalysisappidmonthlyvisittrend?access_token=';
        return $this->analysis($url, $begin_date, $end_date);
    }

    /**
     * 获取分析数据【访问分布】
     * 日期为空，默认是昨天的数据
     * @param string $begin_date 开始日期 如：20180108
     * @param string $end_date 结束日期 如：20180108 ，限定查询1天数据，end_date允许设置的最大值为昨日
     * @return array
     */
    public function getVisitDistribution($begin_date = '', $end_date = '')
    {
        $url = self::API_BASE_URL . 'datacube/getweanalysisappidvisitdistribution?access_token=';
        return $this->analysis($url, $begin_date, $end_date);
    }

    /**
     * 返回访问分布
     * https://mp.weixin.qq.com/debug/wxadoc/dev/api/analysis-visit.html#%E8%AE%BF%E9%97%AE%E5%88%86%E5%B8%83
     * @param $index access_source_session_cnt==访问来源分布,access_staytime_info==访问时长分布,access_depth_info==访问深度的分布
     * @param $key
     * @return string
     */
    public function getVisitDistributionIndex($index, $key)
    {
        $array['access_source_session_cnt'] = [1 => '小程序历史列表', 2 => '搜索', 3 => '会话', 4 => '二维码', 5 => '公众号主页', 6 => '聊天顶部', 7 => '系统桌面', 8 => '小程序主页', 9 => '附近的小程序', 10 => '其他', 11 => '模板消息', 12 => '客服消息', 13 => '公众号菜单', 14 => 'APP分享', 15 => '支付完成页', 16 => '长按识别二维码', 17 => '相册选取二维码', 18 => '公众号文章', 19 => '钱包', 20 => '卡包', 21 => '小程序内卡券', 22 => '其他小程序', 23 => '其他小程序返回', 24 => '卡券适用门店列表', 25 => '搜索框快捷入口', 26 => '小程序客服消息', 27 => '公众号下发', 28 => '会话左下角菜单', 29 => '小程序任务栏'];
        $array['access_staytime_info'] = [1 => '0-2s', 2 => '3-5s', 3 => '6-10s', 4 => '11-20s', 5 => '20-30s', 6 => '30-50s', 7 => '50-100s', 8 => ' > 100s',];
        $array['access_depth_info'] = [1 => '1页', 2 => '2页', 3 => '3页', 4 => '4页', 5 => '5页', 6 => '6-10页', 7 => '>10页'];
        if (isset($array[$key])) {
            return $array[$key];
        } else {
            return '';
        }
    }

    /**
     * 获取分析数据 【访问留存】
     * 日期为空，默认是昨天的数据
     * @param string $begin_date 开始日期 如：20180108
     * @param string $end_date 结束日期 如：20180108 ，限定查询1天数据，end_date允许设置的最大值为昨日
     * @return array
     */
    public function getDailyRetainInfo($begin_date = '', $end_date = '')
    {
        $url = self::API_BASE_URL . 'datacube/getweanalysisappiddailyretaininfo?access_token=';
        return $this->analysis($url, $begin_date, $end_date);
    }

    /**
     * 【访问留存-周留存】
     * 日期为空，默认是上调
     * 注意：时间必须按照自然周的方式输入： 如：20170306(周一), 20170312(周日)
     * @param string $begin_date
     * @param string $end_date
     * @return array
     */
    public function getDailyRetainInfoByweek($begin_date = '', $end_date = '')
    {
        $begin_date = $begin_date ? $begin_date : date("Ymd", mktime(0, 0, 0, date("m"), date("d") - date("w") + 1 - 7, date("Y")));
        $end_date = $end_date ? $end_date : date("Ymd", mktime(23, 59, 59, date("m"), date("d") - date("w") + 7 - 7, date("Y")));
        $url = self::API_BASE_URL . 'datacube/getweanalysisappidweeklyretaininfo?access_token=';
        return $this->analysis($url, $begin_date, $end_date);

    }

    /**
     * 【访问留存-月留存】
     * 日期为空，默认是上月
     * 时间必须按照自然月的方式输入： 如：20170201(月初), 20170228(月末)
     * @param string $begin_date
     * @param string $end_date
     * @return array
     */
    public function getDailyRetainInfoByMonth($begin_date = '', $end_date = '')
    {
        $begin_date = $begin_date ? $begin_date : date("Ymd", mktime(0, 0, 0, date("m") - 1, 1, date("Y")));
        $end_date = $end_date ? $end_date : date("Ymd", mktime(23, 59, 59, date("m"), 0, date("Y")));
        $url = self::API_BASE_URL . 'datacube/getweanalysisappidmonthlyretaininfo?access_token=';
        return $this->analysis($url, $begin_date, $end_date);

    }


    /**
     * 获取分析数据【访问页面】
     * 日期为空，默认是昨天的数据
     * 注意：目前只提供按 page_visit_pv 排序的 top200
     * @param string $begin_date 开始日期 如：20180108
     * @param string $end_date 结束日期 如：20180108 ，限定查询1天数据，end_date允许设置的最大值为昨日
     * @return array
     */
    public function getVisitPage($begin_date = '', $end_date = '')
    {
        $url = self::API_BASE_URL . 'datacube/getweanalysisappidvisitpage?access_token=';
        return $this->analysis($url, $begin_date, $end_date);
    }

    /**
     * 用户画像
     * 获取小程序新增或活跃用户的画像分布数据。时间范围支持昨天、最近7天、最近30天。其中，
     * 新增用户数为时间范围内首次访问小程序的去重用户数，活跃用户数为时间范围内访问过小程序的去重用户数。
     * 画像属性包括用户年龄、性别、省份、城市、终端类型、机型。
     * 注：由于部分用户属性数据缺失，属性值可能出现 “未知”。机型数据无 id 字段，暂只提供用户数最多的 top20。
     * @param string $begin_date 开始日期 如：20180108
     * @param string $end_date 结束日期 20180108
     * @return array
     */
    public function getUserPortrait($begin_date = '', $end_date = '')
    {
        $url = self::API_BASE_URL . 'datacube/getweanalysisappiduserportrait?access_token=';
        return $this->analysis($url, $begin_date, $end_date);
    }


    /**
     * 获取数据通用的方法
     * @param $url 请求 API不需要携带ACCESS_TOKEN
     * @param $begin_date 开始时间
     * @param $end_date 线束时间
     * @return array
     */
    private function analysis($url, $begin_date, $end_date)
    {
        if (!$this->getAccessToken()) return false;
        $url = $url . $this->access_token;
        $beginYesterday = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));
        $endYesterday = mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 1;
        $data = [
            'begin_date' => $begin_date ? $begin_date : date('Ymd', $beginYesterday),
            'end_date' => $end_date ? $end_date : date('Ymd', $endYesterday),
        ];
        return self::checkErrorCode(json_decode(self::httpPost($url, json_encode($data)), true));
    }

    /**
     * 获取缓存
     * @param $name
     * @return mixed
     */
    static private function getCache($name)
    {
        //默认是使用THINKPHP内置缓存，其它可自行更改
        return cache($name);
    }

    /**
     * 设置缓存
     * @param $name
     * @param $value
     * @param $expired
     * @return mixed
     */
    static private function setCache($name, $value, $expired)
    {
        //默认是使用THINKPHP内置缓存，其它可自行更改
        return cache($name, $value, $expired);

    }


    /**
     *
     * 检测登录，按照微信官方要求，登录时序图机制
     * 成功返回后，3rd_seession为KEY,3rd_session_value为值，存在你的 SESSION中或者缓存中
     * @param $code
     * @param $encryptedData
     * @param $iv
     * @param $rawData
     * @param $signature
     * @return bool || 返回用户信息，3rd_session，3rd_session值
     *
     */
    public function checkLogin($code, $encryptedData, $iv, $rawData, $signature)
    {
        if (empty($code)) {
            return false;
        }
        $url = self::API_BASE_URL . 'sns/jscode2session?appid=' . $this->appid . '&secret=' . $this->appsecret . '&js_code=' . $code . '&grant_type=authorization_code';
        if ($result = json_decode(self::httpPost($url, $data = ''), true)) {
            $res = self::checkErrorCode($result);
            $sign = sha1($rawData . $res['session_key']);
            if ($sign != $signature) {
                $this->errcode = Error::$signNoMatching;
                $this->errmsg = Error::$signNoMatchingMsg;
                return false;
            }
            if ($decryptInfo = $this->decryptData($this->appid, $res['session_key'], $encryptedData, $iv)) {
                $data = [];
                $data['userinfo'] = json_decode($decryptInfo, true);
                $data['3rd_session'] = $this->randByDev(16);
                $data['3rd_session_value'] = $result['openid'] . $res['session_key'];
                return $data;
            } else {
                return false;
            }
        }
    }

    /**
     * 检查返回状态若成功返回请求API成功结果,否侧返回FALSE,
     * @param $result array
     * @return bool || array
     */
    private function checkErrorCode($result)
    {
        if (is_array($result)) {
            if (isset($result['errcode']) && $result['errcode'] !== 0) {
                return $this->globalCode($result['errcode'], $result['errmsg']);
            } else {
                return $result;
            }
        }
        return false;
    }

    static public function httpPost($url, $data, $curlFile = false)
    {
        $cl = curl_init();
        curl_setopt($cl, CURLOPT_URL, $url);
        curl_setopt($cl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($cl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($cl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cl, CURLOPT_HEADER, false);
        curl_setopt($cl, CURLOPT_POST, true);
        curl_setopt($cl, CURLOPT_TIMEOUT, 1);
        curl_setopt($cl, CURLOPT_POSTFIELDS, self::buildPostData($data, $curlFile));
        list($content, $status) = array(curl_exec($cl), curl_getinfo($cl), curl_close($cl));
        return (intval($status["http_code"]) === 200) ? $content : false;
    }

    static public function httpGet($url)
    {
        $cl = curl_init();
        curl_setopt($cl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($cl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($cl, CURLOPT_SSLVERSION, 1);
        curl_setopt($cl, CURLOPT_URL, $url);
        curl_setopt($cl, CURLOPT_TIMEOUT, 1);
        curl_setopt($cl, CURLOPT_RETURNTRANSFER, 1);
        list($content, $status) = array(curl_exec($cl), curl_getinfo($cl), curl_close($cl));
        return (intval($status["http_code"]) === 200) ? $content : false;
    }


    static private function buildPostData(&$data, $curlFile)
    {
        if ($curlFile == true) {
            $data = json_decode($data, true);
            if (is_array($data)) {
                foreach ($data as &$value) {
                    if (is_string($value) && $value[0] === '@' && class_exists('CURLFile', false)) {
                        $filename = realpath(trim($value, '@'));
                        file_exists($filename) && $value = new \CURLFile($filename);
                    }
                }
            }
        }
        return $data;

    }

    /**
     * 检验数据信息真实性，并获得解密后的明文
     * @param $appid
     * @param $sessionKey
     * @param $encryptedData 在小程序中 getUserInfo 获得
     * @param $iv
     * @return bool|string
     */
    public function decryptData($appid, $sessionKey, $encryptedData, $iv)
    {
        $aesKey = base64_decode($sessionKey);
        if (strlen($iv) != 24) {
            $this->errcode = Error::$IllegalIv;
            $this->errmsg = Error::$IllegalIvMsg;
            return false;
        }
        $aesIV = base64_decode($iv);
        $aesCipher = base64_decode($encryptedData);
        $result = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
        $dataObj = json_decode($result);
        if ($dataObj == NULL) {
            $this->errcode = Error::$decryptNoData;
            $this->errmsg = Error::$decryptNoDataMsg;
            return false;
        }
        if ($dataObj->watermark->appid != $appid) {
            $this->errcode = Error::$appidNoMatching;
            $this->errmsg = Error::$appidNoMatchingMsg;
            return false;
        }
        return $result;

    }

    /**
     * 生成随机字符串
     * @param $length int 字符串长度
     * @return $str string 随机字符串
     */
    public function getRandChar($length)
    {
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol) - 1;

        for ($i = 0; $i < $length; $i++) {
            $str .= $strPol[rand(0, $max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }
        return str_shuffle($str);
    }


    /**
     *
     * @param $leng
     * @return bool|string
     */
    public function randByDev($leng)
    {
        //微信官方建议：避免使用srand（当前时间）然后rand()的方法，而是采用操作系统提供的真正随机数机制，比如Linux下面读取/dev/urandom设备
        $fp = @fopen('/dev/urandom', 'rb');
        $result = '';
        if ($fp !== FALSE) {
            $result .= @fread($fp, $leng);
            @fclose($fp);
        } else {
            /*
             * 如果你是LINUX用户是打开失败,一般是open_basedir 存在着限制
             * 在PHP.INI 文件找到 open_basedir加上/dev/urandom/如是多个请使用：号分开
             * windosw是没有/dev/urandom设备,可以偿试一下PHP 的 COM 组件，也考虑到有些环境默认是不开启 COM 组件,使用伪随机
             */
            $randStr = '';
            if (@class_exists('COM')) {
                try {
                    $CAPI_Util = new COM('CAPICOM.Utilities.1');
                    $randStr .= $CAPI_Util->GetRandom(16, 0);
                    if ($randStr) {
                        $randStr = md5($randStr, TRUE);
                    }
                } catch (Exception $ex) {
                    $ex->getMessage();
                }
            }
            if (strlen($randStr) < 16) {
                //getRandChar 唯一性并不太高
                return $this->getRandChar($leng);
            } else {
                return $randStr;
            }

        }
        $result = base64_encode($result);
        $result = strtr($result, '+/', '-_');
        return substr($result, 0, $leng);
    }

    /**
     * 返回错误代码内容
     * @param $errcode
     * @param $errmsg
     * @return bool
     */
    private function globalCode($errcode, $errmsg)
    {
        $globalCodes = Error::globalCodes();
        $this->errcode = $errcode;
        if (isset($globalCodes[$errcode])) {
            $this->errmsg = $globalCodes[$errcode];
        } else {
            $this->errmsg = $errmsg;
        }
        return false;
    }


}