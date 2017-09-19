<?php
namespace addons\redPack\model;


use think\Model;

class Redpack extends Model
{

    public function getStatistics($where=[]){
        $result['total_money']=$this->where($where)->sum('money');
        $result['total_record']=$this->where($where)->count('id');
        return $result;
    }

}