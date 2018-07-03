<?php
// +----------------------------------------------------------------------
// | [RhaPHP System] Copyright (c) 2017 http://www.rhaphp.com/
// +----------------------------------------------------------------------
// | [RhaPHP] 并不是自由软件,你可免费使用,未经许可不能去掉RhaPHP相关版权
// +----------------------------------------------------------------------
// | Author: Geeson <qimengkeji@vip.qq.com>
// +----------------------------------------------------------------------


namespace app\mp\controller;


use app\common\model\MpReply;
use think\facade\Request;


class   Material extends Base
{
    /**
     * 同步公众号素材
     * @author geeson 314835050@qq.com
     * @param string $type
     * @param int $page
     * @return \think\response\View
     */
    public function sycMaterial($type = 'image', $offset = 0)
    {

        $weObj = getWechatActiveObj();
        $count = $weObj->getForeverCount();
        $data['mpid'] = $this->mid;
        $url = '';
        $text = '';
        switch ($type) {
            case 'image':
                $image_count = $count['image_count'];
                $images = getForeverMaterial('image', $offset, 50);
                $i = 0;
                if (!empty($images['item'])) {
                    foreach ($images['item'] as $key => $val) {
                        $i++;
                        $data['create_time'] = $val['update_time'];
                        $data['media_id'] = $val['media_id'];
                        $data['title'] = $val['name'];
                        $data['url'] = getHostDomain() . url('mp/Show/image','','').'?url='.urlencode($val['url']);
                        if(!empty($val['url'])){
                            $model = new \app\common\model\Material();
                            $model->addMaterialByMp($type, $data);
                        }
                    }
                    $url = getHostDomain() . url('mp/Material/sycMaterial', ['type' => $type, 'offset' => $offset + $i]);
                    $jdtCss ='100%'; //ceil(($offset / ($image_count)) * 100) . '%';
                    $text = '正在同步'.$offset.'张图片';
                } else {
                    $text = '同步完成';
                    $url = '';
                    $jdtCss = '100%';
                }
                break;
            case 'voice':
                $voice_count = $count['voice_count'];
                $voice = getForeverMaterial('voice', $offset, 20);
                $i = 0;
                if (!empty($voice['item'])) {
                    foreach ($voice['item'] as $key => $val) {
                        $i++;
                        $data['create_time'] = $val['update_time'];
                        $data['media_id'] = $val['media_id'];
                        $data['title'] = $val['name'];
                        $model = new \app\common\model\Material();
                        $model->addMaterialByMp($type, $data);
                    }
                    $url = getHostDomain() . url('mp/Material/sycMaterial', ['type' => $type, 'offset' => $offset + $i]);
                    $jdtCss = ceil(($offset / $voice_count) * 100) . '%';
                    $text = $jdtCss;
                } else {
                    $text = '同步完成';
                    $url = '';
                    $jdtCss = '100%';
                }
                break;
            case 'video':
                $video_count = $count['video_count'];
                $video = getForeverMaterial('video', $offset, 20);
                $i = 0;
                if (!empty($video['item'])) {
                    foreach ($video['item'] as $key => $val) {
                        $i++;
                        $data['create_time'] = $val['update_time'];
                        $data['media_id'] = $val['media_id'];
                        $data['title'] = $val['name'];
                        $model = new \app\common\model\Material();
                        $model->addMaterialByMp($type, $data);
                    }
                    $url = getHostDomain() . url('mp/Material/sycMaterial', ['type' => $type, 'offset' => $offset + $i]);
                    $jdtCss = ceil(($offset / $video_count) * 100) . '%';
                    $text = $jdtCss;
                } else {
                    $text = '同步完成';
                    $url = '';
                    $jdtCss = '100%';
                }
                break;
            case 'news':
                $news_count = $count['news_count'];
                $news = getForeverMaterial('news', $offset, 20);
                $i = 0;
                if (!empty($news['item'])) {
                    foreach ($news['item'] as $key => $val) {
                        $i++;
                        $data['create_time'] = $val['update_time'];
                        $data['media_id'] = $val['media_id'];
                        $data['content'] = json_encode($val['content']);
                        $model = new \app\common\model\Material();
                        $model->addMaterialByMp($type, $data);
                    }
                    $url = getHostDomain() . url('mp/Material/sycMaterial', ['type' => $type, 'offset' => $offset + $i]);
                    $jdtCss = ceil(($offset / $news_count) * 100) . '%';
                    $text = $jdtCss;
                } else {
                    $text = '同步完成';
                    $url = '';
                    $jdtCss = '100%';
                }
                break;
        }

        $this->assign('text', $text);
        $this->assign('jdtCss', $jdtCss);
        $this->assign('url', $url);
        return view('syc');
    }

    public function addMaterial(){
        if(Request::isAjax()){

        }else{

            return view();
        }
    }


    /**
     * 普通上传图片
     */
    public function uploadImg()
    {

        $file = \request()->file('image');
        $info = $file->rule('md5')->validate(['ext' => 'jpg,png,gif,jpeg'])->move(ROOT_PATH . DS . 'uploads');

        if ($info) {
            $info->getSaveName();
            header('Content-Type:application/json; charset=utf-8');
            $res = [
                'code' => 0,

                'data' => [
                    'src' => getHostDomain() . '/uploads' . DS . $info->getSaveName()
                ]

            ];
            return json_encode($res);

        } else {
            // 上传失败获取错误信息
            $res = [
                'code' => 1,
                'msg' => $file->getError()
            ];
            return json_encode($res);
        }
    }

    public function uploadFileBYmpVerify()
    {
        $file = \request()->file('file');
        $info = $file->validate(['ext' => 'mp3,wma,wav,amr,rm,rmvb,wmv,avi,mpg,mpeg,mp4,txt,zip,rar'])->move(ROOT_PATH . DS, '');

        if ($info) {
            $info->getSaveName();
            header('Content-Type:application/json; charset=utf-8');
            $res = [
                'code' => 0,
                'data' => [
                    'src' => getHostDomain() . '/uploads' . DS . $info->getSaveName()
                ]
            ];
            return json_encode($res);


        } else {
            // 上传失败获取错误信息
            $res = [
                'code' => 1,
                'msg' => $file->getError()
            ];
            return json_encode($res);
        }

    }

    public function index($type = '', $from_type = '')
    {
        if ($type == '' || $from_type == '') {
            $this->redirect('mp/Material/index', ['type' => 'image', 'from_type' => 1]);
        }
        $model = new \app\common\model\Material();
        $data = $model->getMaterialList($type, $this->mid, $from_type, 20);
        if ($type == 'news') {
            $news = [];
            foreach ($data as $key => $val) {
                $news[$key]['media_id'] = $val['media_id'];
                $newsItem = json_decode($val['content'], true);
                foreach ($newsItem['news_item'] as $key1 => $val2) {
                    $news[$key]['news_item'][$key1]['title'] = $val2['title'];
                    $news[$key]['news_item'][$key1]['url'] = $val2['url'];
                    $news[$key]['news_item'][$key1]['thumb_url'] = getHostDomain() . url('mp/Show/image','','').'?url='.urlencode($val2['thumb_url']);
                }
            }
            $this->assign('page', $data->render());
            $this->assign('data', $news);
        } else {
            $this->assign('data', $data);
        }

        $this->assign('type', $type);
        $this->assign('fron_type', $from_type);
        return view();
    }

    public function uploadMedia()
    {
        $file = \request()->file('media');
        $info = $file->rule('md5')->validate(['ext' => 'mp3,wma,wav,amr,rm,rmvb,wmv,avi,mpg,mpeg,mp4'])->move(ROOT_PATH . DS . 'uploads');

        if ($info) {
            $info->getSaveName();
            header('Content-Type:application/json; charset=utf-8');
            $res = [
                'code' => 0,
                'data' => [
                    'src' => getHostDomain() . '/uploads' . DS . $info->getSaveName()
                ]
            ];
            return json_encode($res);

        } else {
            // 上传失败获取错误信息
            $res = [
                'code' => 1,
                'msg' => $file->getError()
            ];
            return json_encode($res);
        }
    }
    public function uploadFile()
    {
        $file = \request()->file('media');
        $info = $file->rule('md5')->validate(['ext' => 'mp3,wma,wav,amr,rm,rmvb,wmv,avi,mpg,mpeg,mp4,txt,zip,rar'])->move(ROOT_PATH . DS . 'uploads');

        if ($info) {
            $info->getSaveName();
            header('Content-Type:application/json; charset=utf-8');
            $res = [
                'code' => 0,
                'data' => [
                    'src' => getHostDomain() . '/uploads' . DS . $info->getSaveName()
                ]
            ];
            return json_encode($res);

        } else {
            // 上传失败获取错误信息
            $res = [
                'code' => 1,
                'msg' => $file->getError()
            ];
            return json_encode($res);
        }
    }

    /**
     * 显示二维码图
     * @param $url
     */
    public function qrcode($url)
    {
        header("Content-type: image/png");
        $url = urldecode($url);
        include_once EXTEND_PATH.'/phpqrcode/phpqrcode.php';
        \Qrcode::png($url, $file = false, $level = 'L', $size = 4);
        exit();

    }

    public function getMeterial($type = '', $from_type = 1)
    {
        $model = new \app\common\model\Material();
        $result = $model->getMaterialList($type, $this->mid, $from_type);
        $this->assign('page', $result->render());
        $this->assign('type', $type);
        $this->assign('from_type', $from_type);
        $this->assign('param', input('param'));
        $this->assign('material', $result);
        return view('getmeterial');
    }

    public function getMeterialByImages($type = '', $from_type = 1)
    {
        $model = new \app\common\model\Material();
        $result = $model->getMaterialList($type, $this->mid, $from_type);
        $this->assign('page', $result->render());
        $this->assign('type', $type);
        $this->assign('from_type', $from_type);
        $this->assign('param', input('param'));
        $this->assign('material', $result);
        return view();
    }

    public function sendMaterial($media_id = '', $type = '')
    {
        if (!$media_id || !$type) {
            ajaxMsg(0, '参数不完整');
        }
        $model = new \app\common\model\Material();
        switch ($type) {
            case 'image':
                $result = $model->sendMaterialByImage($media_id);
                if (!$result['errCode']) {
                    ajaxMsg('1', '群发成功');
                } else {
                    ajaxMsg(0, '群发失败:' . json_encode($result));
                }
                break;
        }


    }

    public function delMaterial($media_id = '', $type = '')
    {
        if (!$media_id || !$type) {
            ajaxMsg(0, '参数不完整');
        }
        $model = new \app\common\model\Material();
        $res = $model->delMaterial($media_id, $this->mid);
        if ($res)
            ajaxMsg('1', '删除成功');
        else
            ajaxMsg('0', '删除失败');
    }
}