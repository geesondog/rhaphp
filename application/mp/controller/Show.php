<?php
// +----------------------------------------------------------------------
// | [RhaPHP System] Copyright (c) 2017 http://www.rhaphp.com/
// +----------------------------------------------------------------------
// | [RhaPHP] 并不是自由软件,你可免费使用,未经许可不能去掉RhaPHP相关版权
// +----------------------------------------------------------------------
// | Author: Geeson <qimengkeji@vip.qq.com>
// +----------------------------------------------------------------------


namespace app\mp\controller;


class Show
{
    /**
     * @param string $url
     */
    public function image($url = '')
    {
        $url = urldecode($url);
        if (!empty($Arr = explode('wx_fmt=', $url))) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            $file = curl_exec($ch);
            curl_close($ch);
            $types = array(
                'gif' => 'image/gif',
                'jpeg' => 'image/jpeg',
                'jpg' => 'image/jpeg',
                'png' => 'image/png',
            );
            if(isset($Arr[1]) && key_exists($Arr[1],$types)){
                $type = $types[$Arr[1]];
            }else{
                $type = 'image/jpeg';
            }
            header("Content-type: " . $type);
            echo $file;
            exit();
        }
    }

}