<?php
namespace addons\redPack\controller;


use addons\redPack\model\Redpack;
use app\common\controller\Addon;
use think\facade\Request;

class Index extends Addon
{
    public $adminLogin =true;
    public function index(){
        $input =input();
        $status=isset($input['status'])?$input['status']:'1';
        $model = new Redpack();
        $_lists=$model->alias('a')->where(['a.mpid'=>$input['mid'],'a.addon'=>$input['addon'],'a.status'=>$status])
            ->join('__MP_FRIENDS__ b','a.openid=b.openid')
            ->order('a.create_time DESC')
            ->field('a.*,b.headimgurl,b.nickname')
            ->paginate(20);
        $record=$model->getStatistics(['addon'=>$input['addon'],'mpid'=>$input['mid'],'status'=>$status]);
        $this->assign('status',$status);
        $this->assign('record',$record);
        $this->assign('data',$_lists);
        $this->fetch();
    }

    public function delRedPack(){
        if(Request::isPost()){
            $input =input();
            $model = new Redpack();
            if($model->save(['status'=>'0'],['addon'=>$input['addon'],'mpid'=>$input['mid']])){
                ajaxMsg(1,'操作成功');
            }else{
                ajaxMsg(0,'操作失败');
            }
        }
    }

}