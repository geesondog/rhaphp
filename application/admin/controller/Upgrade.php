<?php
// +----------------------------------------------------------------------
// | [RhaPHP System] Copyright (c) 2017-2020 http://www.rhaphp.com/
// +----------------------------------------------------------------------
// | [RhaPHP] 并不是自由软件,你可免费使用,未经许可不能去掉RhaPHP相关版权
// +----------------------------------------------------------------------
// | Author: Geeson <qimengkeji@vip.qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;


use think\Exception;
use think\facade\Config;
use think\Db;
use think\facade\Request;

class Upgrade extends Base
{
    public function index()
    {
        $data = [];
        $upgradeMsg = '抱歉，系统通信失败，请稍后再试';
        if ($infos = json_decode($this->httpQueryByRhaService(), true)) {
            if (isset($infos['code']) && $infos['code'] == 0) {
                $upgradeMsg = $infos['msg'];
                if (isset($infos['data']) && !empty($infos['data'])) {
                    $data = $infos['data'];
                }
            }
        }
        $this->assign('upgradeMsg', $upgradeMsg);
        $this->assign('data', $data);
        return view();
    }

    public function toUp()
    {
        if (Request::isAjax()) {
            $info = json_decode($data = $this->httpQueryByRhaService('toUp'), true);
            if (!empty($info) && is_array($info) && isset($info['code'])) {
                return $info;
            } elseif (empty($data)) {
                return ['code' => '1', 'msg' => '下载升级包文件失败,请联系官方人员'];
            } else {
                $temFile = ROOT_PATH . '/data/rhaphp.tmp';
                file_put_contents($temFile, $data);
                $zip = new \ZipArchive;
                $res = $zip->open($temFile);
                if ($res === TRUE) {
                    try{
                        $zip->extractTo(ROOT_PATH);
                    }catch (\Exception $exception){
                        return ['code' => '1', 'msg' => '解压失败，请确认当前目录是否有写入权限！'];
                    }
                    $zip->close();
                    $sqlFile = ROOT_PATH . '/data/upgrade.sql';
                    if (is_file($sqlFile)) {
                        $sql = file_get_contents($sqlFile);
                        $sql = str_replace("\r", "\n", $sql);
                        $sql = explode(";\n", $sql);
                        $prefix = Config::get('database.prefix');
                        $orginal = 'rh_';
                        $sql = str_replace(" `{$orginal}", " `{$prefix}", $sql);
                        try {
                            foreach ($sql as $value) {
                                $value = trim($value);
                                if (empty($value)) continue;
                                if (substr($value, 0, 12) == 'CREATE TABLE') {
                                    $name = preg_replace("/^CREATE TABLE `(\w+)` .*/s", "\\1", $value);
                                    if (false !== Db::execute($value)) {

                                    } else {
                                        return ['code' => '1', 'msg' => '创建' . $name . '失败!'];
                                    }
                                } else {
                                    Db::query($value);
                                }
                            }
                        }catch (\Exception $exception){
                            return ['code' => '1', 'msg' => $exception->getMessage()];
                        }
                        unlink($sqlFile);
                        unlink($temFile);
                        //更改版本文档
                        $this->upVersion();
                        return ['code' => '0', 'msg' => '升级完成'];
                    } else {
                        unlink($temFile);
                        $this->upVersion();
                        return ['code' => '0', 'msg' => '升级完成'];
                    }
                } else {
                    unlink($temFile);
                    return ['code' => '1', 'msg' => '安装失败，请确认当前目录是否有写入权限！'];
                }
            }
        }
    }

    public function upVersion(){
        $conf_content = file_get_contents(APP_PATH . 'copyright.php');
        $config = Config::load(APP_PATH . 'copyright.php');
        $new_version_file=ROOT_PATH . '/data/version.txt';
        if(is_file($new_version_file)){
            $new_version= trim(file_get_contents($new_version_file));
            $contents = str_replace("{$config['copyright']['version']}","{$new_version}" , $conf_content);
            if (file_put_contents(APP_PATH . 'copyright.php', $contents)) {
                unlink($new_version_file);
            }else{
                return ['code' => '1', 'msg' => '更新版本号失败，请确认当前目录是否有写入权限！'];
            }
        }
    }

    public function httpQueryByRhaService($method = 'index', $token = '', $data = [])
    {
        $config = Config::load(APP_PATH . 'copyright.php');
        if (isset($config['copyright']['version'])) {
            $version = $config['copyright']['version'];
        } else {
            $version = '';
        }
        $pars = array();
        $pars['host'] = $_SERVER['HTTP_HOST'];
        $pars['version'] = $version;
        $pars['method'] = $method;
        $pars['token'] = $token;
        $ins = array_merge($pars, $data);
        $url = 'https://service.rhaphp.com/gateway';
        $urlset = parse_url($url);
        $headers[] = "Host: {$urlset['host']}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 500);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($ins, '', '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }

}