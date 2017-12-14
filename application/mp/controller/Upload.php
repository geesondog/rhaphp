<?php
// +----------------------------------------------------------------------
// | [RhaPHP System] Copyright (c) 2017 http://www.rhaphp.com/
// +----------------------------------------------------------------------
// | [RhaPHP] 并不是自由软件,你可免费使用,未经许可不能去掉RhaPHP相关版权
// +----------------------------------------------------------------------
// | Author: Geeson <qimengkeji@vip.qq.com>
// +----------------------------------------------------------------------

namespace app\mp\controller;


class Upload
{

    public function uploadImg()
    {

        $file = \request()->file('image');
        $info = $file->rule('md5')->validate(['ext' => 'jpg,png,gif,jpeg'])->move(ROOT_PATH . DS . ENTR_PATH . DS . 'uploads');

        if ($info) {
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
        $info = $file->validate(['ext' => 'mp3,wma,wav,amr,rm,rmvb,wmv,avi,mpg,mpeg,mp4,txt,zip,rar'])->move(ROOT_PATH . DS . ENTR_PATH . DS, '');

        if ($info) {
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

    public function uploadMedia()
    {
        $file = \request()->file('media');
        $info = $file->rule('md5')->validate(['ext' => 'mp3,wma,wav,amr,rm,rmvb,wmv,avi,mpg,mpeg,mp4'])->move(ROOT_PATH . DS . ENTR_PATH . DS . 'uploads');

        if ($info) {
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
        $info = $file->rule('md5')->validate(['ext' => 'mp3,wma,wav,amr,rm,rmvb,wmv,avi,mpg,mpeg,mp4,txt,zip,rar'])->move(ROOT_PATH . DS . ENTR_PATH . DS . 'uploads');

        if ($info) {
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

    public function qiniuUpload()
    {

        $file = \request()->file('image');
        $info = $file->rule('md5')->validate(['ext' => 'jpg,png,gif,jpeg'])->move(ROOT_PATH . DS . ENTR_PATH . DS . 'uploads');

        if ($info) {
            $file = './uploads' . DS . $info->getSaveName();
            if (!empty($mid = session('mid')) || !empty($mid = input('mid'))) {

            }
            $result = qiniuUpload($mid, $file, $info->getFilename());
            header('Content-Type:application/json; charset=utf-8');
            if ($result['code'] == '0') {
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
        $info = $file->rule('md5')->validate(['ext' => 'jpg,png,gif,jpeg'])->move(ROOT_PATH . DS . ENTR_PATH . DS . 'uploads');

        if ($info) {
            header('Content-Type:application/json; charset=utf-8');
            $res = [
                'code' => 1,
                'data' => getHostDomain() . '/uploads' . DS . $info->getSaveName(),
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