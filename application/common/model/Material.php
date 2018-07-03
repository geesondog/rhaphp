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

class Material extends Model
{
    /**
     * 新增本地素材
     * @param $type
     * @param array $data
     * @return bool|int|string
     */
    public function addMaterial($type, $data = [], $materialStatus = 0)
    {
        $data['type'] = $type;
        $data['create_time'] = time();
        $data['from_type'] = $materialStatus;
        switch ($type) {
            case 'text':
                if (!$this->where(['content' => $data['content'], 'mpid' => $data['mpid'], 'type' => $type])->find()) {
                    return $this->allowField(true)->insert($data);
                } else {
                    return false;
                }
                break;
            case 'mews':
                if (!$this->where(['title' => $data['title'], 'mpid' => $data['mpid'], 'type' => $type])->find()) {
                    return $this->allowField(true)->insert($data);
                } else {
                    return false;
                }
                break;
            case 'image':
                if (!$this->where(['url' => $data['url'], 'mpid' => $data['mpid'], 'type' => $type])->find()) {
                    return $this->allowField(true)->insert($data);
                } else {
                    return false;
                }
                break;
            case 'voice':
                if (!$this->where(['url' => $data['url'], 'mpid' => $data['mpid'], 'type' => $type])->find()) {
                    return $this->allowField(true)->insert($data);
                } else {
                    return false;
                }
                break;
            case 'video':
                if (!$this->where(['url' => $data['url'], 'mpid' => $data['mpid'], 'type' => $type])->find()) {
                    return $this->allowField(true)->insert($data);
                } else {
                    return false;
                }
                break;
            case 'music':
                if (!$this->where(['url' => $data['url'], 'mpid' => $data['mpid'], 'type' => $type])->find()) {
                    return $this->allowField(true)->insert($data);
                } else {
                    return false;
                }
                break;
        }


    }


    /**
     * 同步公众号素材
     * @author Geeson 314835050@qq.com
     * @param $type
     * @param array $data
     * @return bool|int|string
     */
    public function addMaterialByMp($type, $data = [])
    {
        $data['type'] = $type;
        $data['from_type'] = 1;
        switch ($type) {
            case 'news':
                if (!$this->where(['media_id' => $data['media_id'], 'mpid' => $data['mpid'], 'type' => $type])->find()) {
                    return $this->allowField(true)->insert($data);
                } else {
                    return $this->updateMaterial(['media_id' => $data['media_id'], 'mpid' => $data['mpid'], 'type' => $type], $data);;
                }
                break;
            case 'image':
                if (!$this->where(['media_id' => $data['media_id'], 'mpid' => $data['mpid'], 'type' => $type])->find()) {
                    return $this->allowField(true)->insert($data);
                } else {
                    return $this->updateMaterial(['media_id' => $data['media_id'], 'mpid' => $data['mpid'], 'type' => $type], $data);

                }
                break;
            case 'voice':
                if (!$this->where(['media_id' => $data['media_id'], 'mpid' => $data['mpid'], 'type' => $type])->find()) {
                    return $this->allowField(true)->insert($data);
                } else {
                    return false;
                }
                break;
            case 'video':
                if (!$this->where(['media_id' => $data['media_id'], 'mpid' => $data['mpid'], 'type' => $type])->find()) {
                    return $this->allowField(true)->insert($data);
                } else {
                    return false;
                }
                break;
        }
    }

    public function updateMaterial($where = [], $data = [])
    {
        return $this->allowField(true)->where($where)->update($data);
    }


    public function getMaterialList($type = 'image', $mpid = '', $from_type = 1, $page = 10)
    {
        if($from_type==0 && input('type') =='image'){
            $model = new Picture();
            $material =  $model->where('mpid',$mpid)
                ->where('type',1)
                ->field('picture as url')
                ->order('id DESC')
                ->paginate($page);
            foreach ($material as $key=>$value){
                    $material[$key]['url']=getHostDomain().DS.$value['url'];
            }
            return $material;
        }else{
            return $this->where(['type' => $type, 'mpid' => $mpid, 'from_type' => $from_type])->order('create_time DESC')->paginate($page);
        }

    }

    /**
     * 全体群发图片消息
     * @author Geeson 314835050@qq.com
     * @param string $media_id
     * @return array|bool
     */
    public function sendMaterialByImage($media_id = '')
    {
        if (!$media_id) {
            return false;
        }
        $data = [
            'filter' => [
                'is_to_all' => true,     //是否群发给所有用户.True不用分组id，False需填写分组id

            ],
            'image' =>
                ["media_id" => $media_id]
            ,
            'msgtype' => 'image',
        ];
        return sendGroupMassMessage($data);
    }

    public function delMaterial($media_id = '', $mpid = '')
    {
        if (!$media_id) {
            return false;
        }
        $weObj = getWechatActiveObj();
        if ($res = $weObj->delForeverMedia($media_id)) {
            $res = $this->where(['media_id' => $media_id, 'mpid' => $mpid])->delete();
        }
        return $res;


    }

    public function getMaterialByFind($where = [], $field = '')
    {
        return $this->where($where)->field($field)->find();
    }
}