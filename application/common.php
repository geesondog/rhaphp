<?php
// +----------------------------------------------------------------------
// | [RhaPHP System] Copyright (c) 2017 http://www.rhaphp.com/
// +----------------------------------------------------------------------
// | [RhaPHP] 并不是自由软件,你可免费使用,未经许可不能去掉RhaPHP相关版权
// +----------------------------------------------------------------------
// | Author: Geeson <qimengkeji@vip.qq.com>
// +----------------------------------------------------------------------

/**
 * 比较两个数组，合并且返回相应值
 * @author geeson 314835050@QQ.COM
 * @param array $arr1
 * @param array $arr2
 * @return array
 */
function diffArrayValue($arr1 = [], $arr2 = [])
{

    $difArr1 = array_diff_key($arr1, $arr2);
    $difArr2 = array_intersect_key($arr1, $arr2);
    $merge = array_merge($difArr1, $difArr2);
    foreach ($merge as $key => $value) {
        foreach ($arr2 as $key2 => $value2) {
            if ($key == $key2) {
                $merge[$key] = $value2;
            }
        }
    }
    return $merge;
}

/**
 * 判断后台登录
 */
function isLogin()
{
    if (!session('admin_id')) {
        Header("HTTP/1.1 303 See Other");
        $url = getHostDomain() . url('admin/Login/index');
        Header("Location: $url");
        exit;
    }
}

/**
 * 获取微信 SDK 调起被动接口对象
 * @author geeson myrhzq@qq.com
 * @param $options array
 */
function getWechatObj($options = [], $mid = '')
{

    if ($mid) {
        $mpInfo = getMpInfo($mid);
        $options = array(
            'appid' => $mpInfo['appid'],
            'appsecret' => $mpInfo['appsecret'],
            'token' => $mpInfo['valid_token'],
            'encodingaeskey' => $mpInfo['encodingaeskey']
        );
    }
    include_once EXTEND_PATH . "wechatSdk/wechat.class.php";
    empty($options) ? $options = session('mp_options') : $options = $options;
    $weObj = new \Wechat($options);
    $weObj->valid();
    $weObj->getRev();
    return $weObj;
}

/**
 * 获取微信 SDK 调起主动对象
 * @author geeson myrhzq@qq.com
 * @param $options array
 */
function getWechatActiveObj($mid = '')
{
    if ($mid == '') {
        if (empty(session('mp_options'))) {
            exit('公众号标识mid不存在');
        }
    }
    if ($mid) {
        $mpInfo = getMpInfo($mid);
        $options = array(
            'appid' => $mpInfo['appid'],
            'appsecret' => $mpInfo['appsecret'],
            'token' => $mpInfo['valid_token'],
            'encodingaeskey' => $mpInfo['encodingaeskey']
        );
    }
    include_once EXTEND_PATH . "wechatSdk/wechat.class.php";
    empty($options) ? $options = session('mp_options') : $options = $options;
    $weObj = new \Wechat($options);
    return $weObj;
}

/**
 * 上传临时素材
 * 上传临时素材，有效期为3天(认证后的订阅号可用)
 * $author geeson 314835050@qq.com
 * @param string  filePath 注意是物理路径
 * @param string 类型：图片:image 语音:voice 视频:video 缩略图:thumb
 */
function uploadMedia($filePath = '', $type = '')
{
    $weObj = getWechatActiveObj();
    $media = $weObj->uploadMedia(['media' => '@' . ROOT_PATH . $filePath], $type);
    if (empty($media)) {
        if ($msg = wxApiResultErrorCode($weObj->errCode)) {
            ajaxMsg(0, $msg);
        } else {
            ajaxMsg(0, 'errCode:' . $weObj->errCode . 'errMsg' . $weObj->errMsg);
        }

    }
    return $media;
}

/**
 * 上传永久素材(认证后的订阅号可用)
 * 新增的永久素材也可以在公众平台官网素材管理模块中看到
 * $param string  filePath 注意是物理路径
 * @param type 类型：图片:image 语音:voice 视频:video 缩略图:thumb
 * @param boolean $is_video 是否为视频文件，默认为否
 * @param array $video_info 视频信息数组，非视频素材不需要提供 array('title'=>'视频标题','introduction'=>'描述')
 * @return boolean|array
 */
function uploadForeverMedia($filePath = '', $type, $is_video = false, $video_info = array())
{

    $weObj = getWechatActiveObj();
    $media = $weObj->uploadForeverMedia(['media' => '@' . ROOT_PATH . $filePath], $type, $is_video, $video_info);
    if (empty($media)) {
        if ($msg = wxApiResultErrorCode($weObj->errCode)) {
            ajaxMsg(0, $msg);
        } else {
            ajaxMsg(0, 'errCode:' . $weObj->errCode . 'errMsg' . $weObj->errMsg);
        }

    }
    return $media;

}

/**
 * 回复文本
 * @author geeson myrhzq@qq.com
 * @param $text string
 */
function replyText($text = null)
{
    if (is_string($text)) {
        $weObj = getWechatObj();
        return $weObj->text($text)->reply();
    }

}

/**
 * 回复图片
 * @param string $media_id
 */

function replyImage($media_id = '')
{

    $weObj = getWechatObj();
    return $weObj->image($media_id)->reply();
}

/**
 * 设置回复音乐
 * @param string $title
 * @param string $desc
 * @param string $musicurl
 * @param string $hgmusicurl
 * @param string $thumbmediaid 音乐图片缩略图的媒体id，非必须
 */
function replyMusic($title, $desc, $musicurl, $hgmusicurl = '', $thumbmediaid = '')
{
    $weObj = getWechatObj();
    return $weObj->music($title, $desc, $musicurl, $hgmusicurl = '', $thumbmediaid = '')->reply();

}

/**
 * 回复语音消息
 * @param string media_id
 *
 */
function replyVoice($media_id = '')
{
    $weObj = getWechatObj();
    return $weObj->voice($media_id)->reply();
}

/*
 * 回复视频消息
 * @param $mediaid string
 * @param $title string
 * @param $description string
 */
function replyVideo($mediaid = '', $title = '', $description = '')
{
    $weObj = getWechatObj();
    return $weObj->video($mediaid, $title, $description)->reply();
}

/**
 * 回复图文
 * @author geeson myrhzq@qq.com
 * @param $news array
 */
function replyNews($new = [])
{
    if (is_array($new)) {
        $weObj = getWechatObj();
        return $weObj->news($new)->reply();
    }

}

/**
 * 与插件API通信
 * @author geeson myrhzq@qq.com
 * @param $name  string 插件名称
 * @param $msg array 微信发来消息数组
 */

function loadAdApi($name = null, $msg = [], $param = [])
{
    $model = new \app\common\model\Addons();
    $_addon = $model->where('addon', '=', $name)->cache('callAddonCache' . $name)->field('id,status')->find();
    if (empty($_addon)) replyText(' No such application exists');
    if ($_addon['status'] != 1) replyText('Application has been withdrawn or not installed');
    session('apiParam', $param);
    $filename = ADDON_PATH . $name . '/controller/Api.php';
    $commonFile = ADDON_PATH . $name . '/Common.php';
    if(file_exists($commonFile)){
        include_once $commonFile;
    }
    session('addonName', $name);
    if (file_exists($filename)) {
        include_once $filename;
        $class = '\addons\\' . $name . '\controller\Api';
        if (class_exists($class)) {
            $apiObj = new $class;
            if (!method_exists($apiObj, 'message')) {
                replyText('Error:' . $filename . 'Controller Method message() Not Exists');
            } else {
                $apiObj->message($msg, $param);
            }
        } else {
            replyText('Error:' . $filename . 'Controller Class Method Not Exists');
        }

    } else {
        replyText('Error:' . $filename . 'Controller Class Method Not Exists');
    }

}

/**
 * 高级群发消息, 根据群组id群发图文消息(认证后的订阅号可用)
 *    注意：视频需要在调用uploadMedia()方法后，再使用 uploadMpVideo() 方法生成，
 *             然后获得的 mediaid 才能用于群发，且消息类型为 mpvideo 类型。
 * @param array $data 消息结构
 * {
 *     "filter"=>array(
 *         "is_to_all"=>False,     //是否群发给所有用户.True不用分组id，False需填写分组id
 *         "group_id"=>"2"     //群发的分组id
 *     ),
 *      "msgtype"=>"mpvideo",
 *      // 在下面5种类型中选择对应的参数内容
 *      // mpnews | voice | image | mpvideo => array( "media_id"=>"MediaId")
 *      // text => array ( "content" => "hello")
 * }
 * @return boolean|array
 */
function sendGroupMassMessage($data)
{
    $weObj = getWechatActiveObj();
    $result = $weObj->sendGroupMassMessage($data);
    if ($result) {
        return $result;
    } else {
        $return['errCode'] = $weObj->errCode;
        $return['errMsg'] = $weObj->errMsg;
        return $return;
    }
}

/**
 * 创建二维码ticket
 * @param int|string $scene_id 自定义追踪id,临时二维码只能用数值型
 * @param int $type 0:临时二维码；1:永久二维码(此时expire参数无效)；2:永久二维码(此时expire参数无效)
 * @param int $expire 临时二维码有效期，最大为604800秒
 * @return array('ticket'=>'qrcode字串','expire_seconds'=>604800,'url'=>'二维码图片解析后的地址')
 */
function get_qrcode($scene_id, $type = 0, $expire = 604800)
{
    $weObj = getWechatActiveObj();
    $result = $weObj->getQRCode($scene_id, $type, $expire);
    if (!$result) {
        $return['errcode'] = 1001;
        $return['errmsg'] = $weObj->errMsg;
        // abort(500, lang('ErrCode:' . $return['errcode'] . ' ErrMsg: ' . $return['errmsg']));
        if ($msg = wxApiResultErrorCode($return['errcode'])) {
            ajaxMsg(0, $msg);
        }
        ajaxMsg(0, 'ErrCode: ' . $return['errcode'] . ' ErrMsg: ' . $return['errmsg']);
    }
    return $result;
}

/**
 * 获取二维码图片
 * @param string $ticket 传入由get_qrcode方法生成的ticket参数
 * @return string url 返回http地址
 */

function getQrRUL($ticket)
{
    $weObj = getWechatActiveObj();
    $result = $weObj->getQRUrl($ticket);
    return $result;


}

/**
 * 长链接转短链接接口
 * @param string $long_url 传入要转换的长url
 * @return boolean|string url 成功则返回转换后的短url
 */
function getQrshortUrl($long_url)
{
    $weObj = getWechatActiveObj();
    return $weObj->getShortUrl($long_url);
}

/**
 * @param string $mid 获取公众号|小程序信息
 * @param string $type 公众号为mp,小程序为miniapp,默认获取公众号信息
 * @param int $expier 缓存有效期 默认1800S
 * @return mixed
 * @author Geeson RhaPHP.COM
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function getMpInfo($mid = '', $type = 'mp', $expier = 1800)
{
    if (empty(input('_mid')) || $type == 'mp') {
        $mid ? $mid : $mid = input('mid');
        $mpInfo = 'mpInfo_' . $mid;
        if ($mid) {
            $mpinfoCahe = \think\facade\Cache::get($mpInfo);
            if (empty($mpinfoCahe)) {
                $mp = \think\Db::name('mp')->where('id', '=', $mid)->find();
                if (!empty($mp)) {
                    \think\facade\Cache::set($mpInfo, $mp, $expier);
                    return $mp;
                } else {
                    abort(500, lang('没有找到相应的公众号信息'));
                }
            } else {
                return $mpinfoCahe;
            }

        } else {
            abort(500, lang('没有找到相应的公众号信息'));
        }
    } else {//这不需要再判断是否为miniapp
        getMimiappInfo($mid, $expier);
    }

}

/**
 * 获取公众号菜单
 * @return array
 */
function getMpMenu()
{
    $weObj = getWechatActiveObj();
    return $weObj->getMenu();
}

/**
 * 删除公众号菜单
 * @return bool
 */
function deleteMpMenu()
{

    $weObj = getWechatActiveObj();
    return $weObj->deleteMenu();
}

/**
 * 创建菜单(认证后的订阅号可用)
 * @param array $data 菜单数组数据
 * type可以选择为以下几种，其中5-8除了收到菜单事件以外，还会单独收到对应类型的信息。
 * 1、click：点击推事件
 * 2、view：跳转URL
 * 3、scancode_push：扫码推事件
 * 4、scancode_waitmsg：扫码推事件且弹出“消息接收中”提示框
 * 5、pic_sysphoto：弹出系统拍照发图
 * 6、pic_photo_or_album：弹出拍照或者相册发图
 * 7、pic_weixin：弹出微信相册发图器
 * 8、location_select：弹出地理位置选择器
 */
function createMpMenu($data = [])
{
    $weObj = getWechatActiveObj();
    if ($weObj->createMenu($data)) {
        return true;
    } else {
        return $weObj;
    }
}

/**
 * 设置、获取公众号类型
 * @arthor geeson 314835050@qq.com
 * @return string
 */

function getMpType($type = '')
{
    switch ($type) {
        case '1':
            return '普通订阅号';
            break;
        case '2':
            return '认证订阅号';
            break;
        case '3':
            return '普通服务号';
            break;
        case '4':
            return '认证服务号(媒体、政府)';
            break;
    }

}

/**
 * 获取应用配置信息
 * @author geeson  myrhzq@qq.com
 * $param $addonName string // 当前应用插件名称
 * @param string  mid 当前公众号标识ID
 * @return array|bool|mixed
 */
function getAddonInfo($addonName = '', $mid = '')
{
    if ($addonName == '' || $mid == '') {
        $addonName = session('addonName');
        $mid = session('mid') ? session('mid') : input('mid');
    }
    if ($addonName == '' || $mid == '') {
        exit('参数不完整：应用名称或者公众号标识不存在');
    }
    $addon = \think\Db::name('addons')->where(['addon' => $addonName])->find();
    $addonInfo = \think\Db::name('addon_info')->where(['addon' => $addonName, 'mpid' => $mid])->find();
    $addon['path'] = ADDON_PATH . $addonName . '/';
    $addon['mp_config'] = isset($addonInfo['infos']) ? json_decode($addonInfo['infos'], true) : [];
    $addon['common_config'] = isset($addon['config']) ? json_decode($addon['config'], true) : [];
    unset($addon['config']);
    return $addon;

}

/**
 * @param string $name 应用标识
 * @param $logoName
 */

function getAddonLogo($name = '', $type = 'mp')
{
    if ($name == '') {
        return false;
    }
    if ($type == 'mp') {
        $model = new \app\common\model\Addons();
        $info = $model->getAddonByFile($name);
        $loginFile = ROOT_PATH . '/addons/' . $name . '/' . $info['logo'];
        if (is_file($loginFile)) {
//        if ($fp = fopen($loginFile, "rb", 0)) {
//            $gambar = fread($fp, filesize($loginFile));
//            fclose($fp);
//            $base64 = chunk_split(base64_encode($gambar));
//            return $encode = 'data:image/jpg/png/gif;base64,' . $base64;
//        }
            return getHostDomain() . '/addons/' . $name . '/' . $info['logo'];
        }
    } elseif ($type == 'miniapp') {
        $model = new \app\common\model\MiniappAddon();
        $info = $model->getAddonByFile($name);
        $loginFile = MINIAPP_PATH . $name . '/' . $info['logo'];
        if (is_file($loginFile)) {
            return getHostDomain() . '/miniapp/' . $name . '/' . $info['logo'];
        }
    }

}

function pr($data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

/**
 * 获取应用配置信息
 * @param author GEESON 314835050@QQ.COM
 * @param string $name 应用标识
 * @param string $key 需获取配置项的键
 * @return bool|string
 */
function getAddonConfigByFile($name = '', $key = '')
{
    if ($name == '') {
        return false;
    }
    $model = new \app\common\model\Addons();
    $info = $model->getAddonByFile($name);
    if (isset($info[$key])) {
        return $info[$key];
    } else {
        return false;
    }
}

function getAdmin()
{
    if (empty(session('admin')) && empty(cookie('admin'))) {
        return false;
    } else {
        $arr1 = session('admin') ? session('admin') : [];
        $arr2 = cookie('admin') ? cookie('admin') : [];
        return $_admin = array_merge($arr1, $arr2);
    }
}

/**
 * 扩展应用 URL 生成
 * @author geeson myrhzq@qq.com
 * @param $url  string 应用url/应用名称/控制器/方法
 * @param $arr array 参数
 */
function addonUrl($url = '', $vars = '', $suffix = true, $domain = false)
{
    if (!empty($addonRule = session('addonRule')) || $url != '') {
        $addonName = isset($addonRule['addon']) ? $addonRule['addon'] : '';
        $addonController = isset($addonRule['col']) ? $addonRule['col'] : '';
        $addonAction = isset($addonRule['act']) ? $addonRule['act'] : '';
        $node = '';
        if ($url == '') {
            $node = $addonName . '/' . $addonController . '/' . $addonAction;
        } else {
            $nodeArr = array_values(array_filter(explode('/', $url)));
            switch (count($nodeArr)) {
                case 1:
                    $node = $addonName . '/' . $addonController . '/' . $nodeArr[0];
                    break;
                case 2:
                    $node = $addonName . '/' . $nodeArr[0] . '/' . $nodeArr[1];
                    break;
                case 3:
                    $node = $node = $nodeArr[0] . '/' . $nodeArr[1] . '/' . $nodeArr[2];
                    break;
            }
        }
        if (!empty($_mid = input('_mid')) || isset($vars['_mid'])) {
            if ($_mid) {
                $_mid = $_mid;
            } elseif (isset($vars['_mid'])) {
                $_mid = $vars['_mid'];
            }
            $url = \think\facade\Url::build('/api/' . $_mid . '/' . $node, $vars, $suffix, $domain);
            return $url = str_replace('.' . config('template.view_suffix'), '', $url);
        } else {
            if (!empty($mid = input('mid'))) {
                if (is_array($vars)) {
                    $vars = array_merge($vars, ['mid' => $mid]);
                } elseif ($vars != '' && !is_array($vars)) {
                    $vars = $vars . '&' . 'mid=' . $mid;
                } else {
                    $vars = ['mid' => $mid];
                }
                $url = \think\facade\Url::build(ADDON_ROUTE . $node, $vars, $suffix, $domain);
                return $url = str_replace('.' . config('template.view_suffix'), '', $url);
            }
        }
    }
}

/*
 * 行为侦听
 * @author geeson myrhzq@qq.com
 * @param $name  string 行为名称
 * @param $arr array 参数
 */
function hook($name = '', $params = [])
{
    think\facade\Hook::listen($name, $params);
}

/*
 * 设置、获取 openid
 */

function getOrSetOpenid($openid = '')
{
    if ($openid != '') {
        session('openid', $openid);
        return $openid;
    } else {
        $openid = session('openid');
        if ($openid) {
            return $openid;
        } else {
            return null;
        }
    }

}

/**
 * @param string $openid
 * @return array|false|PDOStatement|string|\think\Model
 */
function getMemberInfo($openid = '', $field = [])
{
    if ($openid = getOrSetOpenid($openid)) {
        $user = \think\Db::name('mp_friends')->where(['openid' => $openid])->find();
        if (!$field) {
            return $user;
        } else {
            $fields = [];
            foreach ($field as $key => $val) {
                $fields[$val] = $user[$val];
            }
            return $fields;
        }

    }
}

/**
 * 获取粉丝信息(通过 OPENID 获取)
 * @author geeson 314835050@qq.com
 * @param $openid  srting
 * return array
 */
function getFriendInfoForApi($openid = '')
{
    $openid || $openid = session('openid');
    $Obj = getWechatObj();
    return $Obj->getUserInfo($openid);
}

/**
 * 获取当前域名
 * @return string
 */

function getHostDomain()
{
    return \think\facade\Request::domain();
}

/**
 * 获取永久素材列表(认证后的订阅号可用)
 * @param string $type 素材的类型,图片（image）、视频（video）、语音 （voice）、图文（news）
 * @param int $offset 全部素材的偏移位置，0表示从第一个素材
 * @param int $count 返回素材的数量
 * @return boolean|array
 * 返回数组格式:
 * array(
 *  'total_count'=>0, //该类型的素材的总数
 *  'item_count'=>0,  //本次调用获取的素材的数量
 *  'item'=>array()   //素材列表数组，内容定义请参考官方文档
 * )
 */
function getForeverMaterial($type, $offset, $count)
{
    $weObj = getWechatActiveObj();
    return $weObj->getForeverList($type, $offset, $count);
}

/**
 * 生成随机字符串
 * @param $length int 字符串长度
 * @return $str string 随机字符串
 */
function getRandChar($length)
{
    $str = null;
    $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($strPol) - 1;

    for ($i = 0; $i < $length; $i++) {
        $str .= $strPol[rand(0, $max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
    }

    return $str;
}

/**
 * 把多维数组的键值改为小写或大写  改变原数组
 * @param  [type] &$array 要转换的一维或多维数组
 * @param  [type] $case   小写 CASE_LOWER 大写 CASE_UPPER
 * @return [type]         没有返回值
 */
function array_key_case(&$array, $case = CASE_LOWER)
{
    $array = array_change_key_case($array, $case);
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            array_key_case($array[$key], $case);
        }
    }
}


/*
 * 格式化时间
 */
function formatTime($the_time)
{
    $now_time = time();
    $dur = $now_time - $the_time;
    if ($dur < 0) {
        return $the_time;
    } else {
        if ($dur < 60) {
            return $dur . '秒前';
        } else {
            if ($dur < 3600) {
                return floor($dur / 60) . '分钟前';
            } else {
                if ($dur < 86400) {
                    return floor($dur / 3600) . '小时前';
                } else {//昨天
                    //获取今天凌晨的时间戳
                    $day = strtotime(date('Y-m-d', time()));
                    //获取昨天凌晨的时间戳
                    $pday = strtotime(date('Y-m-d', strtotime('-1 day')));
                    if ($the_time > $pday && $the_time < $day) {//是否昨天
                        return $t = '昨天 ' . date('H:i', $the_time);
                    } else {
                        if ($dur < 172800) {
                            return floor($dur / 86400) . '天前';
                        } else {
                            return date('Y-m-d H:i', $the_time);
                        }
                    }
                }
            }
        }
    }
}

/**
 * 验证手机号是否正确
 * @param INT $mobile
 */
function isMobileNumber($mobile)
{
    if (!is_numeric($mobile)) {
        return false;
    }
    return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
}


//邮件地址验证
function validateEmail($email)
{
    $isValid = true;
    $atIndex = strrpos($email, "@");
    if (is_bool($atIndex) && !$atIndex) {
        $isValid = false;
    } else {
        $domain = substr($email, $atIndex + 1);
        $local = substr($email, 0, $atIndex);
        $localLen = strlen($local);
        $domainLen = strlen($domain);
        if ($localLen < 1 || $localLen > 64) {
            $isValid = false;
        } else if ($domainLen < 1 || $domainLen > 255) {
            $isValid = false;
        } else if ($local[0] == '.' || $local[$localLen - 1] == '.') {
            $isValid = false;
        } else if (preg_match('/\\.\\./', $local)) {
            $isValid = false;
        } else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
            $isValid = false;
        } else if (preg_match('/\\.\\./', $domain)) {
            $isValid = false;
        } else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local))) {
            if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", "", $local))) {
                $isValid = false;
            }
        }
        if ($isValid && !(checkdnsrr($domain, "MX") || checkdnsrr($domain, "A"))) {
            $isValid = false;
        }
    }
    return $isValid;
}

/*
 * 分割转换数组
 */

function strExplode($str)
{
    $str = preg_replace("/(\n)|(\s)|(\t)|(\')|(')|(，)/", ',', $str);
    return explode(',', $str);
}

/**
 *求两个已知经纬度之间的距离,单位为千米
 * @param lng1 ,lng2 经度
 * @param lat1 ,lat2 纬度
 * @return float 距离，单位千米
 **/
function getDistance($lat1, $lon1, $lat2, $lon2, $radius = 6378.137)//根据经纬度计算距离
{
    $rad = floatval(M_PI / 180.0);
    $lat1 = floatval($lat1) * $rad;
    $lon1 = floatval($lon1) * $rad;
    $lat2 = floatval($lat2) * $rad;
    $lon2 = floatval($lon2) * $rad;
    $theta = $lon2 - $lon1;
    $dist = acos(sin($lat1) * sin($lat2) +
        cos($lat1) * cos($lat2) * cos($theta)
    );
    if ($dist < 0) {
        $dist += M_PI;
    }
    $dist = $dist * $radius;
    $dist = round($dist);
    if ($dist == '0') {
        return '1km';
    } else {
        return $dist . 'km';
    }
}

//2位小数的随机数
function randomFloat($min = 0, $max = 10)
{
    $num = $min + mt_rand() / mt_getrandmax() * ($max - $min);
    return sprintf("%.2f", $num);

}

function httpGet($url)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
    // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curl, CURLOPT_URL, $url);
    $res = curl_exec($curl);
    curl_close($curl);
    return $res;
}

function httpPost($url, $data, $curlFile = false)
{
    if ($curlFile == true) {
        $data = json_decode($data, true);
        if (is_array($data)) {
            foreach ($data as &$value) {
                if (is_string($value) && $value[0] === '@' && class_exists('CURLFile', false)) {
                    $filename = realpath(trim($value, '@'));
                    file_exists($filename) && $value = new CURLFile($filename);
                }
            }
        }
    }
    $cl = curl_init();
    curl_setopt($cl, CURLOPT_URL, $url);
    curl_setopt($cl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($cl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($cl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cl, CURLOPT_HEADER, false);
    curl_setopt($cl, CURLOPT_POST, true);
    curl_setopt($cl, CURLOPT_TIMEOUT, 60);
    curl_setopt($cl, CURLOPT_POSTFIELDS, $data);
    list($content, $status) = array(curl_exec($cl), curl_getinfo($cl), curl_close($cl));
    return (intval($status["http_code"]) === 200) ? $content : false;
}


/*********************************************************************
 * 函数名称:encrypt
 * 函数作用:加密解密字符串
 * 使用方法:
 * 加密     :encrypt('str','E','nowamagic');
 * 解密     :encrypt('被加密过的字符串','D','nowamagic');
 * 参数说明:
 * $string   :需要加密解密的字符串
 * $operation:判断是加密还是解密:E:加密   D:解密
 * $key      :加密的钥匙(密匙);
 *********************************************************************/
function encrypt($string, $operation, $key = '')
{
    $key = md5($key);
    $key_length = strlen($key);
    $string = $operation == 'D' ? base64_decode($string) : substr(md5($string . $key), 0, 8) . $string;
    $string_length = strlen($string);
    $rndkey = $box = array();
    $result = '';
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($key[$i % $key_length]);
        $box[$i] = $i;
    }
    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if ($operation == 'D') {
        if (substr($result, 0, 8) == substr(md5(substr($result, 8) . $key), 0, 8)) {
            return substr($result, 8);
        } else {
            return '';
        }
    } else {
        return str_replace('=', '', base64_encode($result));
    }
}

/*
 * $data array 返回数据
 * $status string 状态
 * $msg string 提示语
 */
function ajaxReturn($data = [], $status = 1, $msg = '')
{
    header('Content-Type:application/json; charset=utf-8');
    $data = $data;
    $data['status'] = $status;
    if ($msg != '') {
        $data['msg'] = $msg;
    }
    exit(json_encode($data));
}


function ajaxMsg($status = 1, $msg = '')
{
    header('Content-Type:application/json; charset=utf-8');
    $data['status'] = $status;
    $data['msg'] = $msg;
    exit(json_encode($data));
}

function object_array($array)
{
    if (is_object($array)) {
        $array = (array)$array;
    }
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            $array[$key] = object_array($value);
        }
    }
    return $array;
}

function jsonToArray($json)
{
    if ($json) {
        return $arr = object_array(json_decode($json));
    }
}


/**
 * 将list_to_tree的树还原成列表
 * @param  array $tree 原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array $list 过渡用的中间数组，
 * @return array        返回排过序的列表数组
 * @author yangweijie <yangweijiester@gmail.com>
 */
function tree_to_list($tree, $child = 'child', $order = 'id', &$list = array())
{
    if (is_array($tree)) {
        $refer = array();
        foreach ($tree as $key => $value) {
            $reffer = $value;
            if (isset($reffer[$child])) {
                if ($reffer[$child] == null) {

                } else {
                    unset($reffer[$child]);
                    tree_to_list($value[$child], $child, $order, $list);
                }

            }
            $list[] = $reffer;
        }
        $list = list_sort_by($list, $order, $sortby = 'asc');
    }
    return $list;
}

/**
 * 对查询结果集进行排序
 * @access public
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param array $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by($list, $field, $sortby = 'asc')
{
    if (is_array($list)) {
        $refer = $resultSet = array();
        foreach ($list as $i => $data)
            $refer[$i] = &$data[$field];
        switch ($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc':// 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val)
            $resultSet[] = &$list[$key];
        return $resultSet;
    }
    return false;
}

/**
 * xml转换 array
 * @param $xml
 * @return array
 */
function xml_to_array($xml)
{
    $reg = "/<(\\w+)[^>]*?>([\\x00-\\xFF]*?)<\\/\\1>/";
    if (preg_match_all($reg, $xml, $matches)) {
        $count = count($matches[0]);
        $arr = array();
        for ($i = 0; $i < $count; $i++) {
            $key = $matches[1][$i];
            $val = xml_to_array($matches[2][$i]);  // 递归
            if (array_key_exists($key, $arr)) {
                if (is_array($arr[$key])) {
                    if (!array_key_exists(0, $arr[$key])) {
                        $arr[$key] = array($arr[$key]);
                    }
                } else {
                    $arr[$key] = array($arr[$key]);
                }
                $arr[$key][] = $val;
            } else {
                $arr[$key] = $val;
            }
        }
        return $arr;
    } else {
        return $xml;
    }
}

/**
 * 数组转XML
 * @param $array
 * @return string
 * @throws \think\Exception
 */
function array_to_xml($array)
{
    if (!is_array($array)) {
        throw new \think\Exception("参数不是数组！");
    }

    $xml = "<xml>";
    foreach ($array as $key => $val) {
        if (is_numeric($val)) {
            $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
        } else {
            $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
        }
    }
    $xml .= "</xml>";
    return $xml;
}

/**
 * 在数据列表中搜索
 * @access public
 * @param array $list 数据列表
 * @param mixed $condition 查询条件
 * 支持 array('name'=>$value) 或者 name=$value
 * @return array
 */
function list_search($list, $condition)
{
    if (is_string($condition))
        parse_str($condition, $condition);
    // 返回的结果集合
    $resultSet = array();
    foreach ($list as $key => $data) {
        $find = false;
        foreach ($condition as $field => $value) {
            if (isset($data[$field])) {
                if (0 === strpos($value, '/')) {
                    $find = preg_match($value, $data[$field]);
                } elseif ($data[$field] == $value) {
                    $find = true;
                }
            }
        }
        if ($find)
            $resultSet[] =   &$list[$key];
    }
    return $resultSet;
}

function GetRreeByMpMenu($list, $id = 'id', $pid = 'pid', $son = 'sub')
{
    $tree = $map = [];
    foreach ($list as $item) {
        $map[$item[$id]] = $item;
    }
    foreach ($list as $item) {
        if (isset($item[$pid]) && isset($map[$item[$pid]])) {
            $map[$item[$pid]][$son][] = &$map[$item[$id]];
        } else {
            $tree[] = &$map[$item[$id]];
        }
    }
    unset($map);
    return $tree;
}

class Tree
{
    private static $primary = 'id';
    private static $parentId = 'pid';
    private static $child = 'child';

    public static function makeTree(&$data, $index = 0)
    {
        $childs = self::findChild($data, $index);
        if (empty($childs)) {
            return $childs;
        }
        foreach ($childs as $k => &$v) {
            if (empty($data)) break;
            $child = self::makeTree($data, $v[self::$primary]);
            if (!empty($child)) {
                $v[self::$child] = $child;
            }
        }
        unset($v);
        return $childs;
    }

    public static function findChild(&$data, $index)
    {
        $childs = [];
        foreach ($data as $k => $v) {
            if ($v[self::$parentId] == $index) {
                $childs[] = $v;
                unset($v);
            }
        }
        return $childs;
    }

    public static function getTreeNoFindChild($data)
    {
        $map = [];
        $tree = [];
        foreach ($data as &$it) {
            $map[$it[self::$primary]] = &$it;
        }
        foreach ($data as $key => &$it) {
            $parent = &$map[$it[self::$parentId]];
            if ($parent) {
                $parent['child'][] = &$it;
            } else {
                $tree[] = &$it;
                //$tree[]['child'] = null;
            }
        }
        return $tree;
    }

    public static function getParents($data, $catId)
    {
        $tree = array();
        foreach ($data as $item) {
            if ($item[self::$primary] == $catId) {
                if ($item[self::$parentId] > 0)
                    $tree = array_merge($tree, self::getParents($data, $item[self::$parentId]));
                $tree[] = $item;
                break;
            }
        }
        return $tree;
    }
}

function moreArrayUnique($arr = array())
{
    foreach ($arr[0] as $k => $v) {
        $arr_inner_key[] = $k;   //先把二维数组中的内层数组的键值记录在在一维数组中
    }
    foreach ($arr as $k => $v) {
        $v = join(",", $v);    //降维 用implode()也行
        $temp[$k] = $v;      //保留原来的键值 $temp[]即为不保留原来键值
    }
    $temp = array_unique($temp);    //去重：去掉重复的字符串
    foreach ($temp as $k => $v) {
        $a = explode(",", $v);   //拆分后的重组 如：Array( [0] => james [1] => 30 )
        $arr_after[$k] = array_combine($arr_inner_key, $a);  //将原来的键与值重新合并
    }
    return $arr_after;
}

/*
 * 判断移动设备
 */
function isMobile()
{
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
        return true;

    //此条摘自TPM智能切换模板引擎，判断是否为客户端
    if (isset ($_SERVER['HTTP_CLIENT']) && 'PhoneClient' == $_SERVER['HTTP_CLIENT'])
        return true;
    //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA']))
        //找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], 'wap') ? true : false;
    //判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array(
            'nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile'
        );
        //从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    //协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}

//过滤字符空格等
function my_nohtml_mbsubstr($str, $start = 0, $end = 50, $coded = 'utf-8')
{
    $str = strip_tags($str);
    $bf = array(" ", "　", "\t", "\n", "\r");
    $lb = array("", "", "", "", "");
    $str = str_replace($bf, $lb, $str);
    return $str = mb_substr($str, $start, $end, $coded);
}

function _trim($str)
{
    $arr1 = array(" ", "　",);
    $arr2 = array("", "",);
    return str_replace($arr1, $arr2, $str);
}

/**
 * sql执行
 * @param $sqlPath SQL文件
 */
function executeSql($sqlPath)
{
    $sql = file_get_contents($sqlPath);
    $sql = str_replace("\r", "\n", $sql);
    $sql = explode(";\n", $sql);
    $orginal = 'rh_';
    $prefix = \think\facade\Config::get('database.prefix');
    $sql = str_replace("{$orginal}", "{$prefix}", $sql);
    $model = new \app\common\model\Addons();
    foreach ($sql as $value) {
        $value = trim($value);
        if (!empty($value)) {
            if (substr($value, 0, 12) == 'CREATE TABLE') {
                // $name = preg_replace("/^CREATE TABLE `(\w+)` .*/s", "\\1", $value);
                $name = '';
                preg_match('|EXISTS `(.*?)`|', $value, $outValue1);
                preg_match('|TABLE `(.*?)`|', $value, $outValue2);
                if (isset($outValue1[1]) && !empty($outValue1[1])) {
                    $name = $outValue1[1];
                }
                if (isset($outValue2[1]) && !empty($outValue2[1])) {
                    $name = $outValue2[1];
                }
                if (!$name) {
                    ajaxMsg('0', $name . ' SQL语句有误，获取不到表名');
                }
                $res = $model->query("SHOW TABLES LIKE '{$name}'");
                if ($res) {
                    ajaxMsg('0', $name . '表，已经存在');
                }
            }
        }

    }
    foreach ($sql as $value) {
        $value = trim($value);
        if (empty($value)) {
            continue;
        }
        $res = $model->execute($value);
    }
}

function get_server_ip()
{
    if (isset($_SERVER['SERVER_NAME'])) {
        return gethostbyname($_SERVER['SERVER_NAME']);
    } else {
        if (isset($_SERVER)) {
            if (isset($_SERVER['SERVER_ADDR'])) {
                $server_ip = $_SERVER['SERVER_ADDR'];
            } elseif (isset($_SERVER['LOCAL_ADDR'])) {
                $server_ip = $_SERVER['LOCAL_ADDR'];
            }
        } else {
            $server_ip = getenv('SERVER_ADDR');
        }
        return $server_ip ? $server_ip : '获取不到服务器IP';
    }
}

/**
 *
 * @param string $data
 * @param bool $file
 * @param string $level
 * @param int $size
 */
function createQrcode($data = '', $file = false, $level = 'L', $size = 4)
{
    include_once EXTEND_PATH . 'phpqrcode/phpqrcode.php';
    header("Content-type: image/png");
    Qrcode::png($data, $file, $level, $size);
}

/**
 * +----------------------------------------------------------
 * 产生随机字串，可用来自动生成密码 默认长度6位 字母和数字混合
 * +----------------------------------------------------------
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
 * +----------------------------------------------------------
 * @return string
 * +----------------------------------------------------------
 */
function rand_string($len = 6, $type = '', $addChars = '')
{
    $str = '';
    switch ($type) {
        case 0:
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
            break;
        case 1:
            $chars = str_repeat('0123456789', 3);
            break;
        case 2:
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
            break;
        case 3:
            $chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
            break;
        case 4:
            $chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书" . $addChars;
            break;
        default:
            // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
            $chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
            break;
    }
    if ($len > 10) {
        //位数过长重复字符串一定次数
        $chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
    }
    if ($type != 4) {
        $chars = str_shuffle($chars);
        $str = substr($chars, 0, $len);
    } else {
        // 中文随机字
        for ($i = 0; $i < $len; $i++) {
            $str .= msubstr($chars, floor(mt_rand(0, mb_strlen($chars, 'utf-8') - 1)), 1);
        }
    }
    return $str;
}

function getStrings($array = [])
{
    $str = ['a', 'b' . 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '/', ':', ',', '//', '[', ']', '{', '}', '#', '&', '%'];
    $string = '';
    foreach ($array as $k => $v) {
        $string = $str[$v];
    }
    return $string;

}

/**
 * 获取 HTTPS协议类型
 * @return string
 */
function getHttpType()
{
    return $type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
}

/**
 * //
 * @param int $mid 公众号标识|小程序
 * @param string $name 配置项名称
 * @return array|false
 */
function getSetting($mid = 0, $name = '', $cate = '')
{
    $where = ['mpid' => $mid, 'name' => $name];
    if (!empty($cate)) {
        $where['cate'] = $cate;
    } else {
        $param = input();
        if (isset($param['_mid']) && !empty($param['_mid'])) {
            $where['cate'] = 'miniapp';
        } else {
            if (!empty(session('miniapp_options')) && empty(session('mp_options'))) {
                $where['cate'] = 'miniapp';
            } else {
                $where['cate'] = 'mp';
            }
        }
    }
    $model = new \app\common\model\Setting();
    $result = $model->getSetting($where);
    return $result;
}

/**
 * @author Geeson 314835050@qq.com
 * 获取会员（应用如需要使用会员登录，请使用本函数获取会员）
 * 请区别 函数 getMemberInfo
 * @return bool|mixed
 */
function getMember($member_id = '')
{
    $friendModel = new \app\common\model\MpFriends();
    if ($member_id) {
        $member = $friendModel->getMemberInfo(['id' => $member_id]);
    } else {
        if (!$mid = input('mid')) {
            exit('公众号标识mid不存在');
        }
        $c = cookie('member_' . $mid) ? cookie('member_' . $mid) : [];
        $s = session('member_' . $mid) ? session('member_' . $mid) : [];
        $member = array_merge($c, $s);
    }
    if (!empty($member)) {
        $group = \think\Db::name('member_group')->where(['mpid' => $member['mpid']])->order('up_score ASC,up_money ASC,discount ASC')->select();
        $group_id = '0';
        $group_name = '';
        if (!empty($group)) {
            $model = new \app\common\model\MemberWealthRecord();
            $score = $model->getMemberScoreBySum($member['id'], $member['mpid']);
            $money = $model->getMemberMoneyBySum($member['id'], $member['mpid']);

            foreach ($group as $key => $val) {
                if ($val['up_type'] == '0') {
                    if ($score >= $val['up_score'] || $money >= $val['up_money']) {
                        $group_id = $val['gid'];
                        $group_name = $val['group_name'];
                    }
                } elseif ($val['up_type'] == '1') {
                    if ($score >= $val['up_score'] && $money >= $val['up_money']) {
                        $group_id = $val['gid'];
                        $group_name = $val['group_name'];
                    }
                }
            }
            $friendModel->updateMember(['id' => $member['id']], ['group_id' => $group_id]);
        }
        $member = $friendModel->getMemberInfo(['id' => $member['id']]);//可能COOKIE SESSION缓存原因 重新获取最新会员数据
        if (!empty($member)) {
            $group = \think\Db::name('member_group')->where(['gid' => $member['group_id']])->field('gid,group_name,discount')->find();
            if (!empty($group)) {
                $member['gid'] = $group['gid'];
                $member['group_name'] = $group['group_name'];
                $member['discount'] = $group['discount'];
            } else {
                $member['gid'] = '';
                $member['group_name'] = '';
                $member['discount'] = 0;
            }
            return json_decode(json_encode($member), true);
        } else {
            //考虑没有认证号获取基本信息
            return false;
        }
    } else {
        return false;
    }
}

/**
 * 微信支付函数
 * @param  $parment_id 定单id
 * @author geeson <314835050@qq.com>
 * @param int $money
 * @param string $openid
 * @param string $mid 必须
 * @param string $notifyUrl 通知地址
 * @param string $body 商品描述
 * @param string $goods_tag 订单优惠标记
 * @param string $attach 附加数据
 * @param string $trade_type 交易类型
 * @return bool|json数据，可直接填入js函数作为参数
 */
function wxPayByJsApi($parment_id = '', $goods_tag = '', $trade_type = 'JSAPI')
{
    $model = new \app\common\model\Payment();
    if (!$payment = $model->getPaymentByFind(['payment_id' => $parment_id])) {
        return ['errCode' => -1, 'errMsg' => '交易单号不存在'];
    }
    if (setWxpayConfig($payment['mpid'])) {
        $tools = new \JsApiPay();
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($payment['title']);
        $input->SetAttach($payment['attach']);
        $input->SetOut_trade_no($payment['order_number']);
        $input->SetTotal_fee($payment['money'] * 100);
        $input->SetTime_start(date("YmdHis"));//交易起始时间
        $input->SetTime_expire(date("YmdHis", time() + 86400));//交易结束时间
        $input->SetGoods_tag($goods_tag);
        $input->SetNotify_url(\wxPayConfig::$NOTIFY_URL);
        $input->SetTrade_type($trade_type);
        $input->SetOpenid($payment['openid']);
        $order = \WxPayApi::unifiedOrder($input);
        unClient($payment['mpid']);
        if (isset($order['return_code'])) {
            if ($order['return_code'] == 'SUCCESS') {
                $jsApiParameters = $tools->GetJsApiParameters($order);

                if ($jsApiParameters == false) {
                    return ['errCode' => -1, 'errMsg' => '获取API参数失败'];
                } else {
                    return ['errCode' => 'ok', 'data' => $jsApiParameters];
                }
            } else {
                return ['errCode' => -1, 'errMsg' => $order['return_code'] . $order['return_msg']];
            }
        }
    }
    return ['errCode' => -1, 'errMsg' => '没有公众号配置信息'];
}

/**
 * 微信支付-退款
 * @param $parment_id
 * @param null $total_fee 不能有小数点
 * @param null $refund_fee 不能有小数点
 * @return array|成功时返回，其他抛异常
 * @throws WxPayException
 */
function wxPayRefund($parment_id, $total_fee = null, $refund_fee = null)
{
    $model = new \app\common\model\Payment();
    if (!$payment = $model->getPaymentByFind(['payment_id' => $parment_id])) {
        return ['errCode' => -1, 'errMsg' => '交易单号不存在'];
    }
    if (setWxpayConfig($payment['mpid'])) {
        $total_fee = $total_fee ? $total_fee : $payment['money'] * 100;
        $refund_fee = $refund_fee ? $refund_fee : $payment['money'] * 100;
        $input = new \WxPayRefund();
        $input->SetOut_trade_no($payment['order_number']);
        $input->SetTotal_fee($total_fee);
        $input->SetRefund_fee($refund_fee);
        $input->SetOut_refund_no($payment['order_number']);
        $input->SetOp_user_id(WxPayConfig::$MCHID);
        $result = \WxPayApi::refund($input);
        return $result;

    }
    return ['errCode' => -1, 'errMsg' => '没有公众号配置信息'];

}

/**
 * 微信支付-退款查询
 * @param $parment_id
 * @return array|成功时返回，其他抛异常
 * @throws WxPayException
 */
function WxPayRefundQuery($parment_id)
{
    $model = new \app\common\model\Payment();
    if (!$payment = $model->getPaymentByFind(['payment_id' => $parment_id])) {
        return ['errCode' => -1, 'errMsg' => '交易单号不存在'];
    }
    if (setWxpayConfig($payment['mpid'])) {
        $input = new \WxPayRefundQuery();
        $input->SetOut_trade_no($payment['order_number']);
        return \WxPayApi::refundQuery($input);
    }
    return ['errCode' => -1, 'errMsg' => '没有公众号配置信息'];
}

/**
 * @author Geeson <314835050#qq.com>
 * @param string $order_number 订单号
 * @return array errCode ok: 成功 -1：失败
 */
function queryOrder($order_number = '')
{
    $paymentModel = new \app\common\model\Payment();
    if (!$payment = $paymentModel->getPaymentByFind(['order_number' => $order_number])) {
        return ['errCode' => -1, 'errMsg' => '交易单号不存在'];
    }
    if (setWxpayConfig($payment['mpid'])) {
        $input = new \WxPayOrderQuery();
        $input->SetOut_trade_no($order_number);
        $orderRes = \WxPayApi::orderQuery($input);
        unClient($payment['mpid']);
        if (!empty($orderRes)) {
            if (isset($orderRes['trade_state']) && $orderRes['trade_state'] == 'SUCCESS') {//已经支付
                if ($payment['status'] == '0') {//订单状态未处理为成功
                    $C_lk =\think\facade\Cache::get($order_number);
                    if(empty($C_lk)){
                        \think\facade\Cache::set($order_number,'1');
                        $model = new \app\common\model\MemberWealthRecord();
                        if ($model->addMoney($payment['member_id'], $payment['mpid'], $payment['money'], $payment['title'],$order_number)) {
                            \think\facade\Cache::rm($order_number);
                            return ['errCode' => 'ok', 'errMsg' => '交易完成'];
                        } else {
                            \think\facade\Cache::rm($order_number);
                            return ['errCode' => -1, 'errMsg' => '改变账户金额失败'];
                        }
                    }
                } else {
                    return ['errCode' => 'ok', 'errMsg' => '交易完成'];
                }
            } else {
                return ['errCode' => -1, 'errMsg' => '未完成交易'];
            }
        } else {
            return ['errCode' => -1, 'errMsg' => '交易单号不存在'];
        }
    }
    return ['errCode' => -1, 'errMsg' => '没有公众号配置信息'];
}

/**
 * 微信支付回调
 * @return bool
 */
function wxpayNotify()
{
    if ($xml = file_get_contents('php://input')) {
        $xml_parser = xml_parser_create();
        if (!xml_parse($xml_parser, $xml, true)) {
            xml_parser_free($xml_parser);
            return false;
        } else {
            $array = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
            if (isset($array['out_trade_no'])
                && isset($array['openid'])
                && isset($array['mch_id'])
            ) {
                $paymentModel = new \app\common\model\Payment();
                if (!$payment = $paymentModel->getPaymentByFind(['order_number' => $array['out_trade_no']])) {
                    $data = ['return_code' => 'FAIL', 'return_msg' => '定单号不存在'];
                } else {
                    try {
                        setWxpayConfig($payment['mpid']);
                        \WxPayResults::Init($xml);
                        $result = queryOrder($array['out_trade_no']);
                        if ($result['errCode'] == 'ok') {
                            $data = ['return_code' => 'SUCCESS', 'return_msg' => 'OK'];
                        } else {
                            $data = ['return_code' => 'FAIL', 'return_msg' => $result['errMsg']];
                        }
                    } catch (\Exception $exception) {
                        $data = ['return_code' => 'FAIL', 'return_msg' => $exception->getMessage()];
                    }
                }
                echo array_to_xml($data);
            }
        }
    }
}

function unClient($mid = '')
{
    $sslcert = ROOT_PATH . 'data/' . $mid . '_' . '_apiclient_cert.pem';
    $sslkey = ROOT_PATH . 'data/' . $mid . '_' . '_apiclient_key.pem';
    @unlink($sslcert);
    @unlink($sslkey);
}

/**
 * 现金红包
 * 微信规定红包最小金额为1元
 * 本函数输参数金额为元
 * @author geeson 314835050#QQ.COM
 * @param string $mid
 * @param array $param
 * @return array
 */
function sendRedpack($mid = '', $param = [], $addon = '')
{

    if (setWxpayConfig($mid)) {
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack";
        $order_number = time() . rand_string(18, 1);
        $redpackObj = new Redpack();
        $redpackObj->setParments("re_openid", isset($param['openid']) ? $param['openid'] : '');//openid
        $redpackObj->setParments("mch_billno", $order_number);//订单号
        $redpackObj->setParments("nick_name", isset($param['nick_name']) ? $param['nick_name'] : '');//提供方名称|必填
        $redpackObj->setParments("send_name", isset($param['send_name']) ? $param['send_name'] : '');//红包发送者名称|必填
        $redpackObj->setParments("total_amount", isset($param['money']) ? $param['money'] * 100 : '');//付款金额，单位：元、元、元、|必填
        $redpackObj->setParments("min_value", isset($param['min_value']) ? $param['min_value'] : '100');//最小红包金额，单位分
        $redpackObj->setParments("max_value", isset($param['max_value']) ? $param['max_value'] : '100');//最大红包金额，单位分
        $redpackObj->setParments("total_num", isset($param['total_num']) ? $param['total_num'] : '1');//红包収放总人数
        $redpackObj->setParments("wishing", isset($param['wishing']) ? $param['wishing'] : '恭喜发财');//红包祝福诧
        $redpackObj->setParments("client_ip", \think\facade\Request::ip());//调用接口的机器 Ip 地址
        $redpackObj->setParments("act_name", isset($param['act_name']) ? $param['act_name'] : '红包活动');//活动名称
        $redpackObj->setParments("remark", isset($param['remark']) ? $param['remark'] : '红包活动');//备注信息
        $redpackObj->setParments("nonce_str", getRandChar(32));//备注信息
        $xml = $redpackObj->createRedpackXml();
        $result = $redpackObj->xmlToArray($redpackObj->postXmlCurl($xml, $url, true));
        $sslcert = ROOT_PATH . 'data/' . $mid . '_' . '_apiclient_cert.pem';
        $sslkey = ROOT_PATH . 'data/' . $mid . '_' . '_apiclient_key.pem';
        if (isset($result['result_code']) && $result['result_code'] == 'SUCCESS') {
            unlink($sslcert);
            unlink($sslkey);
            $model = new \app\common\model\Redpack();
            $param['order_number'] = $order_number;
            $param['mpid'] = $mid;
            $param['create_time'] = time();
            $param['addon'] = $addon;
            $model->allowField(true)->save($param);
            $param['errCode'] = 0;
            $param['errMsg'] = '发放成功';
            return $param;
        } else {
            unlink($sslcert);
            unlink($sslkey);
            if ($msg = wxApiResultErrorCode($result['result_code'])) {
                return ['errCode' => -1, 'errMsg' => $msg];
            }
            return ['errCode' => -1, 'errMsg' => 'errMsg:' . $result['result_code'] . '：' . $result['return_msg']];
        }
    } else {
        return ['errCode' => -1, 'errMsg' => 'errMsg:该公众号还没有配置支付相关的参数'];
    }
}

/**
 * 设置微信支付配置
 * @author Geeson 314835050@QQ.COM
 * @param $mid 公众号标识
 * @return bool
 */
function setWxpayConfig($mid)
{
    if (!empty($config = getSetting($mid, 'wxpay'))) {
        include_once EXTEND_PATH . 'wxpayAPI/wxPayApi.php';
        \WxPayConfig::$APPID = $config['appid'];
        \WxPayConfig::$APPSECRET = $config['appsecret'];
        \WxPayConfig::$MCHID = $config['mchid'];
        \WxPayConfig::$KEY = $config['paysignkey'];
        \wxPayConfig::$NOTIFY_URL = getHostDomain() . '/index.php';
        $sslcert = ROOT_PATH . 'data/' . $mid . '_' . '_apiclient_cert.pem';
        $sslkey = ROOT_PATH . 'data/' . $mid . '_' . '_apiclient_key.pem';
        file_put_contents($sslcert, isset($config['apiclient_cert']) ? $config['apiclient_cert'] : '');
        file_put_contents($sslkey, isset($config['apiclient_key']) ? $config['apiclient_key'] : '');
        \WxPayConfig::$SSLCERT_PATH = $sslcert;
        \WxPayConfig::$SSLKEY_PATH = $sslkey;

        return true;
    } else {
        //公众号不存在
        return false;
    }
}

/**
 * 重写Url生成
 * @param string $url 路由地址
 * @param string|array $vars 变量
 * @param bool|string $suffix 生成的URL后缀
 * @param bool|string $domain 域名
 * @return string
 */
function url($url = '', $vars = '', $suffix = true, $domain = false)
{
    if (!empty($mid = input('mid'))) {
        if (is_array($vars)) {
            if (isset($vars['mid'])) {
                $mid = $vars['mid'];
            }
            $vars = array_merge($vars, ['mid' => $mid]);
        } elseif ($vars != '' && !is_array($vars)) {
            $vars = $vars . '&' . 'mid=' . $mid;
        } else {
            $vars = ['mid' => $mid];
        }
    }

    return \think\facade\Url::build($url, $vars, $suffix, $domain);
}

/**
 * 普通单发，明确指定内容，如果有多个签名，请在内容中以【】的方式添加到信息内容中，否则系统将使用默认签名
 * @param int $type 短信类型，0 为普通短信，1 营销短信
 * @param string $nationCode 国家码，如 86 为中国
 * @param string $phoneNumber 不带国家码的手机号
 * @param string $msg 信息内容，必须与申请的模板格式一致，否则将返回错误
 * @param string $extend 扩展码，可填空串
 * @param string $ext 服务端原样返回的参数，可填空串
 * @return string json string { "result": xxxxx, "errmsg": "xxxxxx" ... }，被省略的内容参见协议文档
 */
function singleSmsByTx($mid = '', $phoneNumber = '', $msg = '', $type = '0', $nationCode = '86', $extend = "", $ext = "")
{
    include_once EXTEND_PATH . 'Qcloud/Sms/Loader.php';
    if (!$conf = getSetting($mid, 'sms')) {
        return false;//没有配置信息参数
    }
    $appid = isset($conf['txsms']['appid']) ? $conf['txsms']['appid'] : '';
    $appkey = isset($conf['txsms']['appsecret']) ? $conf['txsms']['appsecret'] : '';
    $singleSender = new \Qcloud\Sms\SmsSingleSender($appid, $appkey);
    $result = $singleSender->send($type, $nationCode, $phoneNumber, $msg, "", "");
    $rsp = json_decode($result, true);
    return $rsp;

}

/**
 * @param $mid
 * @param $PhoneNumbers 必填: 短信接收号码
 * @param $SignName 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
 * @param $TemplateCode 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
 * @param array $TemplateParam 必填: 设置模板参数, 假如模板中存在变量需要替换则为必填项
 * @param string $OutId 可选: 设置发送短信流水号
 * @param string $SmsUpExtendCode 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
 * @return bool|mixed|stdClass|string
 */
function singleSmsByAli($mid, $PhoneNumbers, $SignName, $TemplateCode, $TemplateParam = [], $OutId = '', $SmsUpExtendCode = '')
{
    include_once EXTEND_PATH . 'aliyunSms/SignatureHelper.php';
    if (!$conf = getSetting($mid, 'sms')) {
        return false;//没有配置信息参数
    }
    $accessKeyId = isset($conf['alisms']['appid']) ? $conf['alisms']['appid'] : '';
    $accessKeySecret = isset($conf['alisms']['appsecret']) ? $conf['alisms']['appsecret'] : '';
    $helper = new \Aliyun\DySDKLite\SignatureHelper();
    $params = array();
    $params["PhoneNumbers"] = $PhoneNumbers;
    $params["SignName"] = $SignName;
    $params["TemplateCode"] = $TemplateCode;
    $params['TemplateParam'] = $TemplateParam;
    $params['OutId'] = $OutId;
    $params['SmsUpExtendCode'] = $SmsUpExtendCode;
    if (!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
        $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
    }
    $content = $helper->request(
        $accessKeyId,
        $accessKeySecret,
        "dysmsapi.aliyuncs.com",
        array_merge($params, array(
            "RegionId" => "cn-hangzhou",
            "Action" => "SendSms",
            "Version" => "2017-05-25",
        ))
    // fixme 选填: 启用https
    // ,true
    );
    $result = $helper->getErrCode($content->Code);
    if ($result == false) {
        return false;
    } else {
        return $result;
    }


}

/**
 * Geeson 314835050@qq.com
 * @param string $mid 公众号标识
 * @param string $file 上传文件物理路径
 * @param string $key 保护七牛中的文件名
 * @return array
 */
function qiniuUpload($mid = '', $file = '', $key = '')
{
    if (!$mid) {
        return ['code' => 1, 'msg' => '公众号标识mid不能为空'];
    } else {
        $st = getSetting($mid, 'cloud');
        if (!isset($st['qiniu']) && empty($st['qiniu'])) {
            return ['code' => 1, 'msg' => '请先配置七牛云存储参数'];
        } else {
            include_once EXTEND_PATH . 'Qiniu/autoload.php';
            $client = Qiniu\Qiniu::create(array(
                'access_key' => $st['qiniu']['accessKey'],
                'secret_key' => $st['qiniu']['secretKey'],
                'bucket' => $st['qiniu']['bucke'],
                'domain' => $st['qiniu']['domain']
            ));
            $result = $client->uploadFile($file, $key);
            $result = json_decode(json_encode($result), true);
            if (isset($result['response']['code']) && $result['response']['code'] != '200') {
                return ['code' => 1, 'msg' => $result['error']];
            } else {
                return $res = [
                    'code' => 0,
                    'data' => [
                        'src' => $result['data']['url']
                    ]
                ];
            }
        }
    }

}

function dowloadImage($url, $save_dir = './', $filename = '', $type = 0)
{
    if (!file_exists($save_dir) && !@mkdir($save_dir, 0777, true)) {
        ajaxMsg(0, $save_dir . '目录不可写');
    }
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $img = curl_exec($ch);
    curl_close($ch);
    $fp2 = @fopen($save_dir . $filename, 'a');
    fwrite($fp2, $img);
    fclose($fp2);
    unset($img, $url);
    return $save_dir . $filename;
}

/**
 * 向官方获取最新上线应用与风向标
 * @return bool|mixed
 */
function getAppAndWindvaneByApi()
{
    $pars = array();
    $pars['host'] = $_SERVER['HTTP_HOST'];
    $pars['method'] = 'windVane';
    $url = 'https://service.rhaphp.com/gateway';
    $urlset = parse_url($url);
    $headers[] = "Host: {$urlset['host']}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_TIMEOUT, 500);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($pars, '', '&'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $content = curl_exec($ch);
    curl_close($ch);
    return $content;
}

/**
 * @RhaPHP.com geeson
 * 发送客服消息
 * @param array $data 消息结构{"touser":"OPENID","msgtype":"news","news":{...}}
 * @return boolean|array
 */
function sendCustomMessage($data = [])
{
    $wxObj = getWechatActiveObj();
    $result = $wxObj->sendCustomMessage($data);
    if (empty($result)) {
        if ($wxObj->errCode == '45015') {
            return ['errcode' => -1, 'errmsg' => '发送失败：此用户48小时内没有与公众号有过互动'];
        }
    }
    if (isset($result['errcode']) && $result['errcode'] == '0') {
        return $result;
    }
    if ($msg = wxApiResultErrorCode($wxObj->errCode)) {
        return ['errcode' => -1, 'errmsg' => $msg];
    }
    return ['errcode' => -1, 'errmsg' => ' errCode:' . $wxObj->errCode . ' errMsg:' . $wxObj->errMsg];
}

/**
 * 获取临时素材(认证后的订阅号可用)
 * @param string $media_id 媒体文件id
 * @param boolean $is_video 是否为视频文件，默认为否
 * @return raw data
 */
function getMedia($media_id, $is_video = false)
{
    $wxObj = getWechatActiveObj();
    return $result = $wxObj->getMedia($media_id, $is_video = false);

}

/**
 * 获取微信支付页面跳转（授权目录）
 * @param array $mid 公众号 ID 不能为空
 * @param array $param url 携带参数
 * @return string
 */
function getWxPayUrl($mid = '', $param = [])
{
    if (!$mid) {
        return ['code' => -1, 'msg' => '公众号标识ID不能为空'];
    }
    $str = http_build_query($param);
    return getHostDomain() . \think\facade\Url::build('service/payment/wxpay', '', false) . '/?mid=' . $mid . '&' . $str;

}

/**【增加或者修改图文关键词内容】
 * @author geeson rhaphp.com
 * @param string $keyword 关键词
 * @param string $title 标题
 * @param string $picurl 封面URL
 * @param string $desc 描述内容
 * @param string $link 连接地址 （不需要域名），接受addonUrl()与 Url()函数地址
 * @return bool
 */
function setMpKeywordByNews($keyword = '', $title = '', $picurl = '', $desc = '', $link = '')
{
    $mp = getMpInfo();
    if (!$keyword || !$title || !$picurl || !$link || !isset($mp['id'])) {
        return false;//['status'=>0,'msg'=>'参数缺失'];
    }
    $data['mpid'] = $mp['id'];
    $data['keyword'] = $keyword;
    $data['title'] = $title;
    $data['url'] = $picurl;
    $data['content'] = $desc;
    $data['link'] = getHostDomain() . $link;
    $data['type'] = 'news';
    $ruleModel = new \app\common\model\MpRule();
    $replyMode = new \app\common\model\MpReply();
    if ($result = $replyMode->alias('a')->where('a.link', $data['link'])
        ->join('__MP_RULE__ b', 'b.reply_id=a.reply_id')
        ->find()) {
        if ($data['mpid'] != $result['mpid']) {
            ajaxMsg(0, '回复关键词内容公众号标识与当前公众号标识不匹配');
        }
        $replyMode->allowField(true)->save($data, ['reply_id' => $result['reply_id']]);
        $ruleModel->allowField(true)->save(['keyword' => $data['keyword']], ['reply_id' => $result['reply_id']]);
        return true;
    } else {
        if ($res_1 = $replyMode->allowField(true)->save($data)) {
            $data['reply_id'] = $replyMode->reply_id;
            if (!$res_2 = $ruleModel->allowField(true)->save($data)) {
                $replyMode::destroy(['reply_id' => $data['reply_id']]);
            }
        }
        if ($res_1 && $res_2) {
            return $replyMode->reply_id;
        } else {
            return false;
        }
    }


}

/**
 * 【删除关键词回复内容】
 * @author rhaphp.com
 * @param string $reply_id
 * @return bool
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function delKeywordReply($reply_id = '')
{
    $mp = getMpInfo();
    if (!isset($mp['id']) || empty($mp['id']) || !$reply_id) {
        return false;//参数缺失
    }
    $ruleModel = new \app\common\model\MpRule();
    $replyMode = new \app\common\model\MpReply();
    $result = $ruleModel->where(['reply_id' => $reply_id])->find();

    if (!empty($result)) {
        if ($mp['id'] != $result['mpid']) {
            ajaxMsg(0, '回复关键词内容公众号标识与当前公众号标识不匹配');
        }
        $replyMode->where(['reply_id' => $reply_id])->delete();
        $ruleModel->where(['reply_id' => $reply_id])->delete();
        return true;
    }
    return false;

}

/**
 * 通过API状态码返回相应信息
 * @param $code
 * @return bool|mixed
 */
function wxApiResultErrorCode($code)
{
    $codes = [
        '-1' => '系统繁忙，此时请开发者稍候再试', '0' => '请求成功', '40001' => '获取 access_token 时 AppSecret 错误，或者 access_token 无效。请开发者认真比对 AppSecret 的正确性，或查看是否正在为恰当的公众号调用接口', '40002' => '不合法的凭证类型', '40003' => '不合法的 OpenID ，请开发者确认 OpenID （该用户）是否已关注公众号，或是否是其他公众号的 OpenID', '40004' => '不合法的媒体文件类型', '40005' => '不合法的文件类型', '40006' => '不合法的文件大小', '40007' => '不合法的媒体文件 id', '40008' => '不合法的消息类型', '40009' => '不合法的图片文件大小', '40010' => '不合法的语音文件大小', '40011' => '不合法的视频文件大小', '40012' => '不合法的缩略图文件大小', '40013' => '不合法的 AppID ，请开发者检查 AppID 的正确性，避免异常字符，注意大小写', '40014' => '不合法的 access_token ，请开发者认真比对 access_token 的有效性（如是否过期），或查看是否正在为恰当的公众号调用接口', '40015' => '不合法的菜单类型', '40016' => '不合法的按钮个数', '40017' => '不合法的按钮个数', '40018' => '不合法的按钮名字长度', '40019' => '不合法的按钮 KEY 长度', '40020' => '不合法的按钮 URL 长度', '40021' => '不合法的菜单版本号', '40022' => '不合法的子菜单级数', '40023' => '不合法的子菜单按钮个数', '40024' => '不合法的子菜单按钮类型', '40025' => '不合法的子菜单按钮名字长度', '40026' => '不合法的子菜单按钮 KEY 长度', '40027' => '不合法的子菜单按钮 URL 长度', '40028' => '不合法的自定义菜单使用用户', '40029' => '不合法的 oauth_code', '40030' => '不合法的 refresh_token', '40031' => '不合法的 openid 列表', '40032' => '不合法的 openid 列表长度', '40033' => '不合法的请求字符，不能包含 \uxxxx 格式的字符', '40035' => '不合法的参数', '40038' => '不合法的请求格式', '40039' => '不合法的 URL 长度', '40050' => '不合法的分组 id', '40051' => '分组名字不合法', '40060' => '删除单篇图文时，指定的 article_idx 不合法', '40117' => '分组名字不合法', '40118' => 'media_id 大小不合法', '40119' => 'button 类型错误', '40120' => 'button 类型错误', '40121' => '不合法的 media_id 类型', '40132' => '微信号不合法', '40137' => '不支持的图片格式', '40155' => '请勿添加其他公众号的主页链接', '41001' => '缺少 access_token 参数', '41002' => '缺少 appid 参数', '41003' => '缺少 refresh_token 参数', '41004' => '缺少 secret 参数', '41005' => '缺少多媒体文件数据', '41006' => '缺少 media_id 参数', '41007' => '缺少子菜单数据', '41008' => '缺少 oauth code', '41009' => '缺少 openid', '42001' => 'access_token 超时，请检查 access_token 的有效期，请参考基础支持 - 获取 access_token 中，对 access_token 的详细机制说明', '42002' => 'refresh_token 超时', '42003' => 'oauth_code 超时', '42007' => '用户修改微信密码， accesstoken 和 refreshtoken 失效，需要重新授权', '43001' => '需要 GET 请求', '43002' => '需要 POST 请求', '43003' => '需要 HTTPS 请求', '43004' => '需要接收者关注', '43005' => '需要好友关系', '43019' => '需要将接收者从黑名单中移除', '44001' => '多媒体文件为空', '44002' => 'POST 的数据包为空', '44003' => '图文消息内容为空', '44004' => '文本消息内容为空', '45001' => '多媒体文件大小超过限制', '45002' => '消息内容超过限制', '45003' => '标题字段超过限制', '45004' => '描述字段超过限制', '45005' => '链接字段超过限制', '45006' => '图片链接字段超过限制', '45007' => '语音播放时间超过限制', '45008' => '图文消息超过限制', '45009' => '接口调用超过限制', '45010' => '创建菜单个数超过限制', '45011' => 'API 调用太频繁，请稍候再试', '45015' => '回复时间超过限制', '45016' => '系统分组，不允许修改', '45017' => '分组名字过长', '45018' => '分组数量超过上限', '45047' => '客服接口下行条数超过上限', '46001' => '不存在媒体数据', '46002' => '不存在的菜单版本', '46003' => '不存在的菜单数据', '46004' => '不存在的用户', '47001' => '解析 JSON/XML 内容错误', '48001' => 'api 功能未授权，请确认公众号已获得该接口，可以在公众平台官网 - 开发者中心页中查看接口权限', '48002' => '粉丝拒收消息（粉丝在公众号选项中，关闭了 “ 接收消息 ” ）', '48004' => 'api 接口被封禁，请登录 mp.weixin.qq.com 查看详情', '48005' => 'api 禁止删除被自动回复和自定义菜单引用的素材', '48006' => 'api 禁止清零调用次数，因为清零次数达到上限', '48008' => '没有该类型消息的发送权限', '50001' => '用户未授权该 api', '50002' => '用户受限，可能是违规后接口被封禁', '61451' => '参数错误 (invalid parameter)', '61452' => '无效客服账号 (invalid kf_account)', '61453' => '客服帐号已存在 (kf_account exsited)', '61454' => '客服帐号名长度超过限制 ( 仅允许 10 个英文字符，不包括 @ 及 @ 后的公众号的微信号 )(invalid kf_acount length)', '61455' => '客服帐号名包含非法字符 ( 仅允许英文 + 数字 )(illegal character in kf_account)', '61456' => '客服帐号个数超过限制 (10 个客服账号 )(kf_account count exceeded)', '61457' => '无效头像文件类型 (invalid file type)', '61450' => '系统错误 (system error)', '61500' => '日期格式错误', '65301' => '不存在此 menuid 对应的个性化菜单', '65302' => '没有相应的用户', '65303' => '没有默认菜单，不能创建个性化菜单', '65304' => 'MatchRule 信息为空', '65305' => '个性化菜单数量受限', '65306' => '不支持个性化菜单的帐号', '65307' => '个性化菜单信息为空', '65308' => '包含没有响应类型的 button', '65309' => '个性化菜单开关处于关闭状态', '65310' => '填写了省份或城市信息，国家信息不能为空', '65311' => '填写了城市信息，省份信息不能为空', '65312' => '不合法的国家信息', '65313' => '不合法的省份信息', '65314' => '不合法的城市信息', '65316' => '该公众号的菜单设置了过多的域名外跳（最多跳转到 3 个域名的链接）', '65317' => '不合法的 URL', '9001001' => 'POST 数据参数不合法', '9001002' => '远端服务不可用', '9001003' => 'Ticket 不合法', '9001004' => '获取摇周边用户信息失败', '9001005' => '获取商户信息失败', '9001006' => '获取 OpenID 失败', '9001007' => '上传文件缺失', '9001008' => '上传素材的文件类型不合法', '9001009' => '上传素材的文件尺寸不合法', '9001010' => '上传失败', '9001020' => '帐号不合法', '9001021' => '已有设备激活率低于 50% ，不能新增设备', '9001022' => '设备申请数不合法，必须为大于 0 的数字', '9001023' => '已存在审核中的设备 ID 申请', '9001024' => '一次查询设备 ID 数量不能超过 50', '9001025' => '设备 ID 不合法', '9001026' => '页面 ID 不合法', '9001027' => '页面参数不合法', '9001028' => '一次删除页面 ID 数量不能超过 10', '9001029' => '页面已应用在设备中，请先解除应用关系再删除', '9001030' => '一次查询页面 ID 数量不能超过 50', '9001031' => '时间区间不合法', '9001032' => '保存设备与页面的绑定关系参数错误', '9001033' => '门店 ID 不合法', '9001034' => '设备备注信息过长', '9001035' => '设备申请参数不合法', '9001036' => '查询起始值 begin 不合法',
    ];
    if (isset($codes[$code])) {
        return $codes[$code];
    } else {
        return false;
    }
}

/**
 * //获取小程序信息也可以使用 getMpInfo 这个函数
 * @param string $mid 小程序标识
 * @param int $expier 缓存时间
 * @return array|mixed|null|PDOStatement|string|\think\Model
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function getMimiappInfo($mid = '', $expier = 1800)
{
    $mid ? $mid : $mid = input('_mid');
    $mpInfo = 'miniapp_' . $mid;
    if ($mid) {
        $mpinfoCahe = \think\facade\Cache::get($mpInfo);
        if (empty($mpinfoCahe)) {
            $mp = \think\Db::name('miniapp')->where('id', '=', $mid)->find();
            if (!empty($mp)) {
                \think\facade\Cache::set($mpInfo, $mp, $expier);
                return $mp;
            } else {
                abort(500, lang('没有找到相应的小程序信息'));
            }
        } else {
            return $mpinfoCahe;
        }

    } else {
        abort(500, lang('没有找到相应的小程序信息'));
    }
}

/**
 * @param array $options
 * @return \miniprogram\MiniProgram
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function getMiniProgramObj($options = [])
{
    if (empty($options)) {
        $options = \think\facade\Session::get('miniapp_options');
        if (empty($options)) {
            $infos = getMimiappInfo();
            $options['appid'] = $infos['appid'];
            $options['appsecret'] = $infos['appsecret'];
            $options['token'] = $infos['token'];
            $options['encodingaeskey'] = $infos['encodingaeskey'];
        }
    }
    include_once EXTEND_PATH . "miniprogram/MiniProgram.php";
    return new \miniprogram\MiniProgram($options);
}

/**
 * @param $path
 * @return bool
 */
function createDir($path)
{
    if (is_dir($path)) {
        return true;
    }
    if (is_dir(dirname($path))) {
        return mkdir($path);
    }
    createDir(dirname($path));
    return @mkdir($path);
}

/**
 * @param $file_path
 * @param int $type 1：返回缩略图，2：缩小正方图，3：原图
 * @return mixed|null|string
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function getThumb($file_path, $type = 1)
{
    $array = explode('/', $file_path);
    $Name = end($array);
    $model = new \app\common\model\Picture();
    $info = $model->where('name', $Name)->cache(true)->find();
    if (!$info) {
        return null;
    }
    $file = '';
    switch ($type) {
        case 1:
            $file = $info['thumb'];
            break;
        case 2:
            $file = $info['reduce'];
            break;
        case 3:
            $file = $info['picture'];
            break;
    }

    return getHostDomain() . DS . $file;

}

/**
 * 发送模板消息
 * @param array $data 消息结构
 * ｛
 * "touser":"OPENID",
 * "template_id":"ngqIpbwh8bUfcSsECmogfXcV14J0tQlEpBO27izEYtY",
 * "url":"http://weixin.qq.com/download",
 * "topcolor":"#FF0000",
 * "data":{
 * "参数名1": {
 * "value":"参数",
 * "color":"#173177"     //参数颜色
 * },
 * "Date":{
 * "value":"06月07日 19时24分",
 * "color":"#173177"
 * },
 * "CardNumber":{
 * "value":"0426",
 * "color":"#173177"
 * },
 * "Type":{
 * "value":"消费",
 * "color":"#173177"
 * }
 * }
 * }
 * @return boolean|array
 */
function sendTemplateMessage($data = [])
{
    $wxObj = getWechatActiveObj();
    $result = $wxObj->sendTemplateMessage($data);
    if (isset($result['errcode']) && $result['errcode'] == '0') {
        return $result;
    }
    if ($msg = wxApiResultErrorCode($wxObj->errCode)) {
        return ['errcode' => -1, 'errmsg' => $msg];
    }
    return ['errcode' => -1, 'errmsg' => ' errCode:' . $wxObj->errCode . ' errMsg:' . $wxObj->errMsg];

}

function getRuleTypeName($type)
{
    switch ($type) {
        case 'text':
            return '回复文本';
            break;
        case 'news':
            return '回复图文';
            break;
        case 'addon':
            return '触发应用';
            break;
        case 'voice':
            return '回复语音';
            break;
        case 'image':
            return '回复图片';
            break;
        case 'video':
            return '回复视频';
            break;
        case 'music':
            return '回复音乐';
            break;
        case 'multi_news':
            return '回复多图文';
            break;
        default:
            return '未知';
    }
}