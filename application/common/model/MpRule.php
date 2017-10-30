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

class MpRule extends Model
{

    public function delRule($where = [])
    {
        $rule = $this->where($where)->find();
        if (!empty($rule)) {
            if ($rule['reply_id'] > 0) {
                $model = new MpReply();
               $r1= $this->where($where)->delete();
               $r2= $model->delReply(['reply_id' => $rule['reply_id']]);
               if($r1 && $r2){
                   return true;
               }else{
                   return false;
               }
            } else {
               return $this->where($where)->delete();
            }
        }
    }

}