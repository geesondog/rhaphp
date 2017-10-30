<?php
// +----------------------------------------------------------------------
// | [RhaPHP System] Copyright (c) 2017 http://www.rhaphp.com/
// +----------------------------------------------------------------------
// | [RhaPHP] 并不是自由软件,你可免费使用,未经许可不能去掉RhaPHP相关版权
// +----------------------------------------------------------------------
// | Author: Geeson <qimengkeji@vip.qq.com>
// +----------------------------------------------------------------------


namespace app\common\model;


use think\Model;

class Setting extends Model
{
    /**
     * @param array $where array('mpid'=>,name=>'')
     * @param array $value
     * @return $this|int|string
     */
    public function addSetting($where = [], $value = [])
    {
        $json = json_encode($value);
        if ($this->where($where)->find()) {
            return $this->where($where)->update(['value' => $json]);
        } else {
            return $this->insert(array_merge($where, ['value' => $json]));
        }
    }

    /**
     * @param array $where
     * @return array|false|\PDOStatement|string|Model
     */
    public function getSetting($where = [])
    {
        $result = $this->where($where)->field('value')->find();
        if (!empty($result) && isset($result['value'])) {
            if ($result = json_decode($result['value'], true)) {
                return $result;
            }
        } else {

            return false;
        }
    }

}