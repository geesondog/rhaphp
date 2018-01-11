<?php
// 检测环境是否支持可写
define('IS_WRITE', true);

/**
 * 系统环境检测
 * @return array 系统环境数据
 */
function check_env()
{
    $items = array(
        'os' => array('操作系统', '不限制', '类Unix', PHP_OS, 'success'),
        'php' => array('PHP版本', '5.6.0', '5.6+', PHP_VERSION, 'success'),
        'upload' => array('附件上传', '不限制', '2M+', '未知', 'success'),
        'gd' => array('GD库', '2.0', '2.0+', '未知', 'success'),
        'disk' => array('磁盘空间', '30M', '不限制', '未知', 'success'),
    );

    //PHP环境检测
    if ($items['php'][3] < $items['php'][1]) {
        $items['php'][4] = 'error';
        session('error', true);
    }

    //附件上传检测
    if (@ini_get('file_uploads'))
        $items['upload'][3] = ini_get('upload_max_filesize');

    //GD库检测
    $tmp = function_exists('gd_info') ? gd_info() : array();
    if (empty($tmp['GD Version'])) {
        $items['gd'][3] = '未安装';
        $items['gd'][4] = 'error';
        session('error', true);
    } else {
        $items['gd'][3] = $tmp['GD Version'];
    }
    unset($tmp);

    //磁盘空间检测
    if (function_exists('disk_free_space')) {
        $items['disk'][3] = floor(disk_free_space(ROOT_PATH) / (1024 * 1024)) . 'M';
    }

    return $items;
}

/**
 * 目录，文件读写检测
 * @return array 检测数据
 */
function check_dirfile()
{
    $items = array(
        array('dir', '可写', 'success', 'addons'),
        array('dir', '可写', 'success', 'application'),
        array('dir', '可写', 'success', 'data'),
        array('dir', '可写', 'success', 'config'),
        array('dir', '可写', 'success', 'runtime'),
        array('dir', '可写', 'success', 'uploads'),

    );

    foreach ($items as &$val) {
        if('dir' == $val[0]){
            if(!is_writable(ROOT_PATH . $val[3])) {
                if(is_dir(ROOT_PATH . $val[3])) {
                    $val[1] = '可读';
                    $val[2] = 'error';
                    session('error', true);
                } else {
                    $val[1] = '不存在';
                    $val[2] = 'error';
                    session('error', true);
                }
            }
            if(file_exists(ROOT_PATH . $val[3])) {
                if(!is_writable(ROOT_PATH . $val[3])) {
                    $val[1] = '不可写';
                    $val[2] = 'error';
                    session('error', true);
                }
            } else {

                if(!is_writable(dirname(ROOT_PATH . $val[3]))) {
                    $val[1] = '不存在';
                    $val[2] = 'error';
                    session('error', true);
                }
            }
        }
    }
    return $items;
}

/**
 * 函数检测
 * @return array 检测数据
 */
function check_func()
{
    $items = array(
        array('pdo', '支持', 'success', '类'),
        array('pdo_mysql', '支持', 'success', '模块'),
        array('file_get_contents', '支持', 'success', '函数'),
        array('mb_strlen', '支持', 'success', '函数'),
    );

    foreach ($items as &$val) {
        if (('类' == $val[3] && !class_exists($val[0]))
            || ('模块' == $val[3] && !extension_loaded($val[0]))
            || ('函数' == $val[3] && !function_exists($val[0]))
        ) {
            $val[1] = '不支持';
            $val[2] = 'error';
            session('error', true);
        }
    }

    return $items;
}

/**
 * 写入配置文件
 * @param  array $config 配置信息
 */
function write_config($config)
{
    if (is_array($config)) {
        //读取配置内容
        $conf = file_get_contents(ROOT_PATH . 'data/db.tpl');
        //替换配置项
        foreach ($config as $name => $value) {
            $conf = str_replace("[{$name}]", $value, $conf);
        }

        file_put_contents(APP_PATH . '/install.lock', 'ok');

        //写入应用配置文件
        if (file_put_contents(APP_PATH . '../config/database.php', $conf)) {
            show_msg('配置文件写入成功');
        } else {
            show_msg('配置文件写入失败！', 'error');
            session('error', true);
        }
        return '';
    }
}

/**
 * 创建数据表
 * @param  resource $db 数据库连接资源
 */
function create_tables($db, $prefix = '')
{
    //读取SQL文件
    if(!is_file(ROOT_PATH . 'data/sql.sql')){
        show_msg(ROOT_PATH . 'data/sql.sql 安装数据库文件不存在');
        exit;
    }
    $sql = file_get_contents(ROOT_PATH . 'data/sql.sql');
    $sql = str_replace("\r", "\n", $sql);
    $sql = explode(";\n", $sql);

    //替换表前缀
    $orginal = 'rh_';
    $sql = str_replace(" `{$orginal}", " `{$prefix}", $sql);

    //开始安装
    show_msg('开始安装数据库...');
    foreach ($sql as $value) {
        $value = trim($value);
        if (empty($value)) continue;
        if (substr($value, 0, 12) == 'CREATE TABLE') {
           // $name = preg_replace("/^CREATE TABLE `(\w+)` .*/s", "\\1", $value);
            $name='';
            preg_match('|EXISTS `(.*?)`|',$value,$outValue1);
            preg_match('|TABLE `(.*?)`|',$value,$outValue2);
            if(isset($outValue1[1]) && !empty($outValue1[1])){
                $name=$outValue1[1];
            }
            if(isset($outValue2[1]) && !empty($outValue2[1])){
                $name=$outValue2[1];
            }
            $msg = "创建数据表{$name}";
            if (false !== $db->execute($value)) {
                show_msg($msg . '...成功');
            } else {
                show_msg($msg . '...失败！', 'error');
                session('error', true);
            }
        } else {
            $db->execute($value);
        }

    }
}

function register_administrator($db, $prefix, $admin)
{
    show_msg('开始注册创始人帐号...');

    $salt = rand_string();
    $password = md5($admin['password'] . $salt);

    $sql = "INSERT INTO `[PREFIX]admin` (`id`,`admin_name`,`password`,`status`,`ip`,`last_time`,`rand_str`,`admin_id`) VALUES " .
        "('1', '[NAME]', '[PASS]','1','[IP]','[TIME]','[STR]','1');";
    $sql = str_replace(
        array('[PREFIX]', '[NAME]', '[PASS]','[IP]','[TIME]','[STR]'),
        array($prefix, $admin['username'], $password, \think\facade\Request::ip(), time(), $salt),
        $sql);
    $db->execute($sql);
    show_msg('创始人帐号注册完成！');
}

/**
 * 及时显示提示信息
 * @param  string $msg 提示信息
 */
function show_msg($msg, $class = 'primary')
{
    echo "<script type=\"text/javascript\">showmsg(\"{$msg}\", \"{$class}\")</script>";

    //ob_flush();
    //flush();
}

/**
 * 生成系统AUTH_KEY
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function build_auth_key()
{
    $chars = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $chars .= '`~!@#$%^&*()_+-=[]{};:"|,.<>/?';
    $chars = str_shuffle($chars);
    return substr($chars, 0, 40);
}

