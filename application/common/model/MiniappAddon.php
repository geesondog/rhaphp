<?php
// +----------------------------------------------------------------------
// | [RhaPHP System] Copyright (c) 2017 http://www.rhaphp.com/
// +----------------------------------------------------------------------
// | [RhaPHP] 并不是自由软件,你可免费使用,未经许可不能去掉RhaPHP相关版权
// +----------------------------------------------------------------------
// | Author: Geeson <qimengkeji@vip.qq.com>
// +----------------------------------------------------------------------
namespace app\common\model;


use think\Db;
use think\Model;

class MiniappAddon extends Model
{
    /**
     *获取数据库中应用信息
     * @param $addonName 应用标识
     * @author Geeson 314835050@qq.com
     */
    public function getAddonByDb($addonName = '')
    {
        if ($addonName == null) {
            return false;
        }
        $info = $this->where(['addon' => $addonName])->find();
        if ($info) {
            if(isset($info['config']) && !empty($info['config'])){
                $info['config']=json_decode($info['config'],true);
            }
            return $info;
        } else {
            return false;
        }

    }

    /**
     *获取文件中应用信息
     * @param $addonName 应用标识
     * @author Geeson 314835050@qq.com
     */
    public function getAddonByFile($addonName = '')
    {
        if ($addonName == null) {
            return false;
        }

        $path = MINIAPP_PATH . $addonName . DS . 'Config.php';
        if (is_file($path)) {
            $addonInfo = include $path;
            if (empty($addonInfo)) {
                return false;
            }
            if ($addonInfo['addon'] != $addonName) {
                return false;
            }
            return $addonInfo;
        } else {
            return false;
        }
    }

    /**
     * 获取当前公众号对当前应用参数配置
     * @param $addonName 当前应用标识
     * @param $mid 当前公众号标识
     * @return bool|mixed|null
     */
    public function getAaddonConfigByMp($addonName,$mid)
    {
        if ($addonName == null) {
            return false;
        }

        $result = Db::name('miniapp_addon_info')->where(['mpid' => $mid, 'addon' => $addonName])->find();
        if (!empty($result)) {
            return json_decode($result['infos'], true);
        } else {
            return null;
        }
    }

}