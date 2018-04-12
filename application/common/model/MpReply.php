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

class MpReply extends Model
{
    protected $pk='reply_id';
    /**
     * @param array $where
     * @param string $order
     * @param int $page
     */
    public function getMaterialList($where = [], $order = '', $page = 10)
    {
        return $this->where($where)->order($order)->paginate($page);
    }

    public function delReply($where = [])
    {
        return $this->where($where)->delete();
    }

}



