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
use think\facade\Request;
use think\facade\Session;
use think\Image;

class Upload {

    protected $type = 0;
    protected $mid;
    protected $thumbPath;
    protected $reducePath;

    public function __construct() {
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

    public function deleteFile() {
        if (!Request::isAjax() && !Request::isPost()) {
            return false;
        }
        $picture = Db::name('picture')->where(['mpid' => $this->mid, 'id' => input('picId')])->find();
        unlink(ROOT_PATH . $picture['thumb']);
        unlink(ROOT_PATH . $picture['picture']);
        unlink(ROOT_PATH . $picture['reduce']);
        Db::name('picture')->delete(['mpid' => $this->mid, 'id' => input('picId')]);
    }

    /**
     * 新增一个获取本地所有图片的函数，用于百度编辑器
     * @return type
     */
    public function getPicture() {

        $in = input();

        $model = new Picture();

        $result = $model->where('mpid', $this->mid)
            ->where('type', $this->type)
            ->order('create_time DESC')
            ->field('reduce as url')
            ->limit($in['start'], $in['size'])->select();
        $list = [];
        foreach ($result as $key => $val) {
            $list[$key]['url'] = getHostDomain() . DIRECTORY_SEPARATOR . $val['url'];
            $list[$key]['path'] = DIRECTORY_SEPARATOR . $val['url'];
        }
        return json_encode(array(
            "state" => "SUCCESS",
            "start" => $in['start'],
            "list" => $list,
            "total" => $result->count()
        ));
    }

    /**
     * 新增一个上传，针对百度编辑器
     */
    public function uploadUeditorImg() {
        if (!Request::isAjax() && !Request::isPost()) {
            return false;
        }
        $file = \request()->file('upfile'); //兼容百度编辑器
        $info = $file->rule('md5')->validate(['ext' => 'jpg,png,gif,jpeg'])->move(ROOT_PATH . '/' . ENTR_PATH . '/' . 'uploads');
        if ($info) {
            $saveName = str_replace('\\', '/', $info->getSaveName());
            $_array = explode('/', $saveName);
            $Name = end($_array);
            if (Picture::get(['name' => $Name])) {
                // 上传失败获取错误信息
                $res = [
                    'code' => 1,
                    'msg' => '图片名称重复,请从素材选取' . $Name
                ];
                return json_encode($res);
            }

            $picture = 'uploads' . '/' . $saveName;
            $image_path = \think\facade\Env::get('root_path') . $picture;

            if (!is_file($image_path)) {
                // 上传失败获取错误信息
                $res = [
                    'code' => 1,
                    'msg' => '图片写入失败'
                ];
                return json_encode($res);
            }

            if ($array = explode('/', $saveName)) {
                if (createDir($this->thumbPath . $array[0])) {
                    $thumb = $this->thumbPath . $saveName;
                    $image = Image::open($image_path);
                    $image->thumb(300, 300, \think\Image::THUMB_SCALING)->save($thumb);
                    if (createDir($this->reducePath . $array[0])) {
                        $reduce = $this->reducePath . $saveName;
                        $image->thumb(600, 600, \think\Image::THUMB_SCALING)->save($reduce);
                    }
                    $_data = [
                        'name' => $Name,
                        'mpid' => $this->mid,
                        'type' => $this->type,
                        'thumb' => 'uploads/thumb/' . $saveName,
                        'picture' => 'uploads/' . $saveName,
                        'reduce' => 'uploads/reduce/' . $saveName,
                        'create_time' => time()
                    ];
                    $model = new Picture();
                    $model->save($_data);
                }
            }

            header('Content-Type:application/json; charset=utf-8');
            $res = [
                'state' => 'SUCCESS',
                'url' => getHostDomain() . '/uploads/' . $saveName,
                'title' => $Name
            ];
            return json_encode($res);
        } else {
            // 上传失败获取错误信息
            $res = [
                'state' => $file->getError()
            ];
            return json_encode($res);
        }
    }

    public function uploadImg() {
        if (!Request::isAjax() && !Request::isPost()) {
            return false;
        }
        $file = \request()->file('image');
        $info = $file->rule('md5')->validate(['ext' => 'jpg,png,gif,jpeg'])->move(ROOT_PATH . '/' . ENTR_PATH . '/' . 'uploads');

        if ($info) {
            $saveName = str_replace('\\', '/', $info->getSaveName());
            $_array = explode('/', $saveName);
            $Name = end($_array);

            if ($PictureInfo = Picture::get(['name' => $Name])) {//若有重复，则返回图片
                $res = [
                    'code' => 0,
                    'data' => [
                        'picId' => $PictureInfo['id'],
                        'src' => getHostDomain() . '/uploads/' . $saveName
                    ]
                ];
                return json_encode($res);

            }

            $picture = 'uploads' . '/' . $saveName;
            $image_path = \think\facade\Env::get('root_path') . $picture;

            if (!is_file($image_path)) {
                // 上传失败获取错误信息
                $res = [
                    'code' => 1,
                    'msg' => '图片写入失败'
                ];
                return json_encode($res);
            }

            if ($array = explode('/', $saveName)) {
                if (createDir($this->thumbPath . $array[0])) {
                    $thumb = $this->thumbPath . $saveName;
                    $image = Image::open($image_path);
                    $image->thumb(300, 300, \think\Image::THUMB_SCALING)->save($thumb);
                    if (createDir($this->reducePath . $array[0])) {
                        $reduce = $this->reducePath . $saveName;
                        $image->thumb(600, 600, \think\Image::THUMB_SCALING)->save($reduce);
                    }
                }
            }

            $_data = [
                'name' => $Name,
                'mpid' => $this->mid,
                'type' => $this->type,
                'thumb' => 'uploads/thumb/' . $saveName,
                'picture' => 'uploads/' . $saveName,
                'reduce' => 'uploads/reduce/' . $saveName,
                'create_time' => time()
            ];
            $picId = Db::name('picture')->insertGetId($_data);
            $res = [
                'code' => 0,
                'data' => [
                    'picId' => $picId,
                    'src' => getHostDomain() . '/uploads/' . $saveName
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

    public function uploadFileBYmpVerify() {
        if (!Request::isAjax() && !Request::isPost()) {
            return false;
        }
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

    public function uploadMedia() {
        if (!Request::isAjax() && !Request::isPost()) {
            return false;
        }
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

    public function uploadFile() {
        if (!Request::isAjax() && !Request::isPost()) {
            return false;
        }
        $file = \request()->file('media');
        $info = $file->rule('md5')->validate(['ext' => 'mp3,wma,wav,amr,rm,rmvb,wmv,avi,mpg,mpeg,mp4,txt,zip,rar'])->move(ROOT_PATH . '/' . ENTR_PATH . '/uploads');

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

    public function qiniuUpload() {
        if (!Request::isAjax() && !Request::isPost()) {
            return false;
        }
        $file = \request()->file('image');
        $info = $file->rule('md5')->validate(['ext' => 'jpg,png,gif,jpeg'])->move(ROOT_PATH . '/' . ENTR_PATH . '/' . 'uploads');
        if ($info) {
            $saveName = str_replace('\\', '/', $info->getSaveName());
            $file = './uploads/' . $saveName;
            if (!empty($mid = session('mid')) || !empty($mid = input('mid'))) {

            }
            if ($_mid = input('_mid')) {
                $mid = $_mid;
            }
            $result = qiniuUpload($mid, $file, $saveName);
            header('Content-Type:application/json; charset=utf-8');
            if ($result['code'] == '0') {

                $_array = explode('/', $saveName);
                $Name = end($_array);
                if (!Picture::get(['name' => $Name])) {
                    $picture = 'uploads/' . $saveName;
                    $image_path = \think\facade\Env::get('root_path') . $picture;
                    if (is_file($image_path)) {
                        if ($array = explode('/', $saveName)) {
                            if (createDir($this->thumbPath . $array[0])) {
                                $thumb = $this->thumbPath . $saveName;
                                $image = Image::open($image_path);
                                $image->thumb(260, 146, \think\Image::THUMB_CENTER)->save($thumb);
                                if (createDir($this->reducePath . $array[0])) {
                                    $reduce = $this->reducePath . $saveName;
                                    $image->thumb(260, 260, \think\Image::THUMB_CENTER)->save($reduce);
                                }
                                $_data = [
                                    'name' => $Name,
                                    'mpid' => $this->mid,
                                    'type' => $this->type,
                                    'thumb' => 'uploads/thumb/' . $saveName,
                                    'picture' => $result['data']['src'],
                                    'reduce' => 'uploads/reduce/' . $saveName,
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

    public function uploaderMediaNewsImg() {
        if (!Request::isAjax() && !Request::isPost()) {
            return false;
        }
        $file = \request()->file('file_upload');
        $info = $file->rule('md5')->validate(['ext' => 'jpg,png,gif,jpeg'])->move(ROOT_PATH . '/' . ENTR_PATH . '/uploads');

        if ($info) {
            header('Content-Type:application/json; charset=utf-8');
            $saveName = str_replace('\\', '/', $info->getSaveName());
            $res = [
                'code' => 1,
                'data' => getHostDomain() . '/uploads/' . $saveName,
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
