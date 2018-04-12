<?php
// +----------------------------------------------------------------------
// | [RhaPHP System] Copyright (c) 2017 http://www.rhaphp.com/
// +----------------------------------------------------------------------
// | [RhaPHP] 并不是自由软件,你可免费使用,未经许可不能去掉RhaPHP相关版权
// +----------------------------------------------------------------------
// | Author: Geeson <qimengkeji@vip.qq.com>
// +----------------------------------------------------------------------


namespace app\mp\controller;

use app\common\model\MpMsg;
use think\facade\Request;

class Message extends Base
{


    public function messageList($status=0){
        $model =new MpMsg();
        $result=$model->messageListByGroup($this->mid,$status);
        $this->assign('msgList',$result);
        $this->assign('status',$status);
        return view('messagelist');
    }

    /**
     * rhaPHP
     * @param $openid
     * @param string $msg_content
     * @return \think\response\View
     */
    public function replyMsg($openid,$msg_content=''){
        if(Request::isPost()){
           if(empty($msg_content)){
               ajaxMsg(0,'消息不能为空');
           }
           $data=[
               'touser'=>$openid,
               'msgtype'=>'text',
               'text'=>[
                   'content'=>$msg_content
               ]
           ];
           $result=sendCustomMessage($data);
           if($result['errcode'] !=0){
                ajaxMsg(0,$result['errmsg']);
           }else{
               $model =new MpMsg();
               $model->save(['status'=>1],['openid'=>$openid]);
              // $mpInfo=getMpInfo($this->mid);
               $msg['openid']=$openid;
               $msg['mpid']=$this->mid;
               $msg['create_time']=time();
               $msg['type']='text';
               $msg['content']=$msg_content;
               $msg['status']=1;
               $msg['is_reply']=1;
               $model->allowField(true)->insert($msg);
               ajaxMsg(1,'发送成功');
           }
        }else{
            $model =new MpMsg();
            $msgLists=$model->getFriendMsgList($openid,$this->mid);
            $this->assign('openid',$openid);
            $this->assign('msgList',$msgLists);
            return view('replymsg');
        }

    }


    public function delMsg($id,$openid){
        if(Request::isAjax()){
            $model =new MpMsg();
            if($model->where(['msg_id'=>$id,'openid'=>$openid,'mpid'=>$this->mid])->delete()){
                ajaxMsg(1,'操作成功');
            } else{
                ajaxMsg(0,'操作失败');
            }
        }
    }

    public function replyImgByMsg($openid){
        $file = \request()->file('image');
        $info = $file->rule('md5')->validate(['ext' => 'jpg,png,gif,jpeg'])->move(ROOT_PATH . DS . 'uploads');
        if ($info) {
            $info->getSaveName();
            $imgPath='/uploads' . DS . $info->getSaveName();
            $result=uploadMedia($imgPath,'image');
            if(isset($result['media_id']) && $result['media_id']!=''){
                $data=[
                    'touser'=>$openid,
                    'msgtype'=>'image',
                    'image'=>[
                        'media_id'=>$result['media_id']
                    ]
                ];
                $result=sendCustomMessage($data);
                if(isset($result['errcode']) && $result['errcode']=='0' && isset($result['errmsg']) && $result['errmsg']=='ok'){
                    $model =new MpMsg();
                    $model->save(['status'=>1],['openid'=>$openid]);
                    // $mpInfo=getMpInfo($this->mid);
                    $msg['openid']=$openid;
                    $msg['mpid']=$this->mid;
                    $msg['create_time']=time();
                    $msg['type']='image';
                    $msg['content']=getHostDomain().$imgPath;
                    $msg['status']=1;
                    $msg['is_reply']=1;
                    $model->allowField(true)->insert($msg);
                    $res = [
                        'code' => 0,
                        'msg' => '发送成功'
                    ];
                    return json_encode($res);
                }else{
                    $res = [
                        'code' => 1,
                        'msg' => $result['errmsg']
                    ];
                    return json_encode($res);
                }
            }
            unlink($imgPath);
        } else {
            ajaxMsg(0,$file->getError());
        }
    }

    public function getMsgStatusTotal($openid=''){
       $model= new MpMsg();
       $result=$model->getMsgTotal($openid,$this->mid);
       ajaxReturn(['msgTotal'=>$result],1);
    }

    public function playVideo($video_id=''){
        echo getMedia($video_id);
    }


}