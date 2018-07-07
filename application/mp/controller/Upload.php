<?php
// +----------------------------------------------------------------------
// | [RhaPHP System] Copyright (c) 2017 http://www.rhaphp.com/
// +----------------------------------------------------------------------
// | [RhaPHP] 并不是自由软件,你可免费使用,未经许可不能去掉RhaPHP相关版权
// +----------------------------------------------------------------------
// | Author: Geeson <qimengkeji@vip.qq.com>
// +----------------------------------------------------------------------

namespace app\mp\controller;


use app\common\model\Picture;
use think\Db;
use think\Env;
use think\facade\Session;
use think\Image;

class Upload
{
    protected $type = 0;
    protected $mid;
    protected $thumbPath;
    protected $reducePath;

    public function __construct()
    {
        $sMid = Session::get('mid');
        $sMid = $sMid ? $sMid : 0;
        $s_Mid = Session::get('_mid');
        $s_Mid = $s_Mid ? $s_Mid : 0;
        $OP2 = Session::get('miniapp_options');
        if (!empty($OP2) && empty(input('_mid'))) {
            $this->type = 2;
            $this->mid = input('_mid') ? input('_mid') : $s_Mid;
        } else {
            $this->type = 1;
            $this->mid = input('mid') ? input('mid') : $sMid;
        }
        $this->thumbPath = \think\facade\Env::get('root_path') . 'uploads/thumb/';
        $this->reducePath = \think\facade\Env::get('root_path') . 'uploads/reduce/';
    }

    public function uploadImg()
    {
        $file = \request()->file('image');
        $info = $file->rule('md5')->validate(['ext' => 'jpg,png,gif,jpeg'])->move(ROOT_PATH .'/'. ENTR_PATH . '/' . 'uploads');
        if ($info) {
            $_array = explode('/', $info->getSaveName());
            $Name = end($_array);
            if (!Picture::get(['name' => $Name])) {
                $picture = 'uploads' . '/' . $info->getSaveName();
                $image_path = \think\facade\Env::get('root_path') . $picture;
                if (is_file($image_path)) {
                    if ($array = explode('/', $info->getSaveName())) {
                        if (createDir($this->thumbPath . $array[0])) {
                            $thumb = $this->thumbPath . $info->getSaveName();
                            $image = Image::open($image_path);
                            $image->thumb(260, 146, \think\Image::THUMB_CENTER)->save($thumb);
                            if (createDir($this->reducePath . $array[0])) {
                                $reduce = $this->reducePath . $info->getSaveName();
                                $image->thumb(260, 260, \think\Image::THUMB_CENTER)->save($reduce);
                            }
                            $_data = [
                                'name' => $Name,
                                'mpid' => $this->mid,
                                'type' => $this->type,
                                'thumb' => 'uploads/thumb/'. $info->getSaveName(),
                                'picture' => 'uploads/' . $info->getSaveName(),
                                'reduce' => 'uploads/reduce/'. $info->getSaveName(),
                                'create_time' => time()
                            ];
                            $model = new Picture();
                            $model->save($_data);
                        }
                    }
                }
            }

            header('Content-Type:application/json; charset=utf-8');
            $res = [
                'code' => 0,

                'data' => [
                    'src' => getHostDomain() . '/uploads/' . $info->getSaveName()
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
        $info = $file->validate(['ext' => 'mp3,wma,wav,amr,rm,rmvb,wmv,avi,mpg,mpeg,mp4,txt,zip,rar'])->move(ROOT_PATH . '/' . ENTR_PATH . '/', '');

        if ($info) {
            header('Content-Type:application/json; charset=utf-8');
            $res = [
                'code' => 0,
                'data' => [
                    'src' => getHostDomain() . '/uploads/' . $info->getSaveName()
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

    public function uploadMedia()
    {
        $file = \request()->file('media');
        $info = $file->rule('md5')->validate(['ext' => 'mp3,wma,wav,amr,rm,rmvb,wmv,avi,mpg,mpeg,mp4'])->move(ROOT_PATH . '/' . ENTR_PATH . '/uploads');

        if ($info) {
            header('Content-Type:application/json; charset=utf-8');
            $res = [
                'code' => 0,
                'data' => [
                    'src' => getHostDomain() . '/uploads/' . $info->getSaveName()
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
        $info = $file->rule('md5')->validate(['ext' => 'mp3,wma,wav,amr,rm,rmvb,wmv,avi,mpg,mpeg,mp4,txt,zip,rar'])->move(ROOT_PATH . '/' . ENTR_PATH  .'/uploads');

        if ($info) {
            header('Content-Type:application/json; charset=utf-8');
            $res = [
                'code' => 0,
                'data' => [
                    'src' => getHostDomain() . '/uploads/'. $info->getSaveName()
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

    public function qiniuUpload()
    {
        $file = \request()->file('image');
        $info = $file->rule('md5')->validate(['ext' => 'jpg,png,gif,jpeg'])->move(ROOT_PATH . '/'. ENTR_PATH . '/' . 'uploads');
        if ($info) {
            $file = './uploads/' . $info->getSaveName();
            if (!empty($mid = session('mid')) || !empty($mid = input('mid'))) {
            }
            if ($_mid = input('_mid')) {
                $mid = $_mid;
            }
            $result = qiniuUpload($mid, $file, $info->getFilename());
            header('Content-Type:application/json; charset=utf-8');
            if ($result['code'] == '0') {
                $_array = explode('/', $info->getSaveName());
                $Name = end($_array);
                if (!Picture::get(['name' => $Name])) {
                    $picture = 'uploads/' . $info->getSaveName();
                    $image_path = \think\facade\Env::get('root_path') . $picture;
                    if (is_file($image_path)) {
                        if ($array = explode('/', $info->getSaveName())) {
                            if (createDir($this->thumbPath . $array[0])) {
                                $thumb = $this->thumbPath . $info->getSaveName();
                                $image = Image::open($image_path);
                                $image->thumb(260, 146, \think\Image::THUMB_CENTER)->save($thumb);
                                if (createDir($this->reducePath . $array[0])) {
                                    $reduce = $this->reducePath . $info->getSaveName();
                                    $image->thumb(260, 260, \think\Image::THUMB_CENTER)->save($reduce);
                                }
                                $_data = [
                                    'name' => $Name,
                                    'mpid' => $this->mid,
                                    'type' => $this->type,
                                    'thumb' => 'uploads/thumb/' . $info->getSaveName(),
                                    'picture' => $result['data']['src'],
                                    'reduce' => 'uploads/reduce/' . $info->getSaveName(),
                                    'create_time' => time()
                                ];
                                $model = new Picture();
                                $model->save($_data);
                            }
                        }
                    }
                }
                return json_encode($result);
            } else {
                return json_encode($result);
            }
        } else {
            // 上传失败获取错误信息
            $res = [
                'code' => 1,
                'msg' => $file->getError()
            ];
            return json_encode($res);
        }
    }

    public function uploaderMediaNewsImg()
    {
        $file = \request()->file('file_upload');
        $info = $file->rule('md5')->validate(['ext' => 'jpg,png,gif,jpeg'])->move(ROOT_PATH . '/'. ENTR_PATH . '/uploads');

        if ($info) {
            header('Content-Type:application/json; charset=utf-8');
            $res = [
                'code' => 1,
                'data' => getHostDomain() . '/uploads/' . $info->getSaveName(),
                'message' => '上完成功'
            ];
            return json_encode($res);

        } else {
            // 上传失败获取错误信息
            $res = [
                'code' => 0,
                'data' => '',
                'message' => $info->getError()
            ];
            return json_encode($res);
        }
    }

}