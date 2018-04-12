<?php
/**
 * User: Geeson 314835050@qq.com
 * Date: 2017/9/9
 * Time: 18:22 中国·上海
 */

namespace addons\touPiao\model;


use think\Model;

class VoteBaoming extends Model
{

    public function getBaomingList($where=[],$order='bm_id DESC',$row=10){
       return $this->where($where)
            ->order($order)
            ->paginate($row);
    }
}