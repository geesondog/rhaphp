<?php
// +----------------------------------------------------------------------
// | [RhaPHP System] Copyright (c) 2017 http://www.rhaphp.com/
// +----------------------------------------------------------------------
// | [RhaPHP] 并不是自由软件,你可免费使用,未经许可不能去掉RhaPHP相关版权
// +----------------------------------------------------------------------
// | Author: Geeson <qimengkeji@vip.qq.com>
// +----------------------------------------------------------------------

namespace app\behavior;


use think\facade\Request;

class Upload
{

    /**
     * @author geeson 314835050@qq.com
     * @param type : image 单图 images 多图
     * @param name INPUT name值
     * @param null $param
     */
    public function run($param = null)
    {
        if (ENTR_PATH == '') {
            $IMGpath = '/public';
        } else {
            $IMGpath = '';
        }
        switch ($param['type']) {
            case 'image':
                if (isset($param['value'])) {
                    if (is_array($param['value'])) {
                        $imagesPath = '';
                    } else {
                        $imagesPath = $param['value'];
                    }
                    if ($imagesPath == '') {
                        $imagesPath = getHostDomain() . $IMGpath . '/static/images/def.jpg';
                    }
                    $value = $imagesPath;
                } else {
                    $imagesPath = getHostDomain() . $IMGpath . '/static/images/def.jpg';
                    $value = '';
                }
                if (!empty($mid = session('mid')) || !empty($mid = input('mid'))) {
                    $sting = getSetting($mid, 'cloud');
                    if (isset($sting['qiniu']['status']) && $sting['qiniu']['status'] == 1) {
                        $param['cloud'] = 'qiniu';
                    }
                }
                if (isset($param['cloud'])) {
                    if ($param['cloud'] == 'qiniu') {
                        $uploadUrl = url('mp/Upload/qiniuUpload');
                    }
                } else {
                    $uploadUrl = url('mp/Upload/uploadImg');
                }
                echo "<style>.rhaphp-hook-upload-image{overflow: hidden;}  .upload-imgs{border: 1px solid #e6e6e6; padding: 3px; display: inline-block; margin-right: 5px;}  .upload-imgs img {width: 150px; height: 90px;}  .upload-imgs-del{cursor: pointer;position: absolute;  font-size: 18px;}</style><div class='rhaphp-hook-upload-image'>
                            <div class='rhaphp-upload-thumb'  style=\"padding: 5px; border: #e6e6e6 solid 1px; float: left; \">
                                <img class=\"form_{$param['name']}\" src=\"{$imagesPath}\" width=\"150\" height=\"90\">
                            </div>
                            <div  class='rhaphp-upload' style=\"margin-bottom: 10px; margin-left: 5px; float: left; \">
                                <input style=\"display: none\" type=\"text\" value=\"{$value}\" name=\"{$param['name']}\" >
                                <button style='margin-bottom: 5px;' type=\"button\" class=\"layui-btn layui-btn-primary\" id=\"up_{$param['name']}\"><i class=\"layui-icon\">&#xe681;</i>上传图片</button>
                                ";
                if (isset($param['material'])) {
                    $getMeterial_url = url("mp/Material/getMeterial", "", "") . "/type/'+type+'/param/'+paramName";
                    echo "<BR><span onclick=\"getMaterial('{$param['name']}','image')\" class=\"layui-btn layui-btn-primary btn-sc\"><i class=\"layui-icon\">&#xe654;</i>选择素材</span><script>function getMaterial(paramName,type){layer.open({type: 2,title: '选择素材',shadeClose: true,shade: 0.1,area: ['750px', '480px'],
content: '{$getMeterial_url}
     })}
 function controllerByVal(value,paramName,type) {
        $('.form_'+paramName).attr('src',value);
        $(\"input[name=\"+paramName+\"]\").val(value);
    }
</script>";}
                echo "</div>
                        </div>
                        <script>
                            layui.use('upload', function(){
                                var load;
                                var upload = layui.upload;
                                  var uploadInst = upload.render({
                                    elem: '#up_{$param['name']}'
                                    ,url: \"$uploadUrl\"
                                    ,accept: 'images' //普通文件
                                    ,field:'image'
                                    ,before: function(input){
                                          load = layer.load(1);
                                     }
                                    ,done: function(res){
                                      if(res.code==0){                                    
                                            $('input[name=\"{$param['name']}\"]').val(res.data.src)
                                            $('.form_{$param['name']}').attr('src',res.data.src);
                                             layer.close(load);
                                        }else{
                                            layer.close(load);
                                            layer.alert(res.msg);
                                        }
                                    }
                                    ,error: function(){
                                      //请求异常回调
                                    }
                                  });               
                            });
                        </script>";
                break;
            case 'images':
                if (isset($param['value'])) {
                    $imagesPath = $param['value'];
                    if ($imagesPath == '') {
                        $imagesPath = getHostDomain() . $IMGpath . '/static/images/def.jpg';
                    }
                    $value = $param['value'];
                } else {
                    $imagesPath = getHostDomain() . $IMGpath . '/static/images/def.jpg';
                    $value = '';
                }
                $html = '';
                if (is_array($value)) {
                    foreach ($value as $key => $val) {
                        $html .= '<div  class="upload-imgs"><img src=' . $val . '><span class="upload-imgs-del" onclick="delImg(this)">X</span>';
                        $html .= '<input type="hidden" name=' . $param['name'] . '[' . $key . ']" value="' . $val . '"></div>';
                    }
                }
                if (!empty($mid = session('mid')) || !empty($mid = input('mid'))) {
                    $sting = getSetting($mid, 'cloud');
                    if (isset($sting['qiniu']['status']) && $sting['qiniu']['status'] == 1) {
                        $param['cloud'] = 'qiniu';
                    }
                }
                if (isset($param['cloud'])) {
                    if ($param['cloud'] == 'qiniu') {
                        $uploadUrl = url('mp/Upload/qiniuUpload');
                    }
                } else {
                    $uploadUrl = url('mp/Upload/uploadImg');
                }
                echo "<style>.rhaphp-hook-upload-image{overflow: hidden;}  .upload-imgs{border: 1px solid #e6e6e6; padding: 3px; display: inline-block; margin-right: 5px;}  .upload-imgs img {width: 150px; height: 90px;}  .upload-imgs-del{cursor: pointer;position: absolute;  font-size: 18px;}</style><div class='rhaphp-hook-upload-image'>
                            <div class='upload-images-list'>
                                <div class='upload-images-list list_{$param['name']}'>
                                {$html}
                                </div>        
                            </div>
                            <div  class='rhaphp-upload' style=\"margin:5px 5px 0px 0px;; float: left; \">
                              
                                <button type=\"button\" class=\"layui-btn layui-btn-primary\" id=\"up_{$param['name']}\"><i class=\"layui-icon\">&#xe681;</i>多图上传</button>
                                ";
                if (isset($param['material'])) {
                    $getMeterial_url = url("mp/Material/getMeterialByImages", "", "") . "/type/'+type+'/param/'+paramName";
                    echo "<span onclick=\"getMaterial('{$param['name']}','image')\" class=\"layui-btn layui-btn-primary btn-sc\"><i class=\"layui-icon\">&#xe654;</i>选择素材</span><script>function getMaterial(paramName,type){layer.open({type: 2,title: '选择素材',shadeClose: true,shade: 0.1,area: ['750px', '480px'],
content: '{$getMeterial_url}
     })}
 function controllerByValByImages(value,paramName,type) {
                $('.list_'+paramName).append('<div class=\"upload-imgs\"><img src=\"'+value+'\"><span class=\"upload-imgs-del\" onclick=\"delImg(this)\">X</span><input name=\"'+paramName+'[]\" type=\"hidden\" value=\"'+value+'\"></div>')}
</script>";}
                echo "</div>
                        </div>
                        <script>
                            layui.use('upload', function(){
                                var load;
                                var upload = layui.upload;
                                  var uploadInst = upload.render({
                                    elem: '#up_{$param['name']}'
                                    ,url: \"$uploadUrl\"
                                    ,accept: 'images' //普通文件
                                    ,field:'image'
                                    ,before: function(input){
                                          load = layer.load(1);
                                     }
                                    ,done: function(res){
                                       var key=rnd(0,100);
                                      if(res.code==0){
                                          $('.list_{$param['name']}').append('<div class=\"upload-imgs\"><img src='+res.data.src+'><span class=\"upload-imgs-del\" onclick=\"delImg(this)\">X</span><input name=\"{$param['name']}['+key+']\" type=\"hidden\" value='+res.data.src+'></div>')
                                       
                                             layer.close(load);
                                        }else{
                                            layer.close(load);
                                            layer.alert(res.msg);
                                        }
                                    }
                                    ,error: function(){
                                      //请求异常回调
                                    }
                                  });               
                            });
                             function rnd(n, m){
                                var random = Math.floor(Math.random()*(m-n+1)+n);
                                return random;
                                }
                                function delImg(obj) {
                                  $(obj).parent().remove();
                                }
                        </script>";
                break;
            case 'media':
                if (isset($param['value'])) {
                    $value = $param['value'];
                } else {
                    $value = '';
                }
                $uploadUrl = url('mp/Upload/uploadMedia');
                echo "<div class='rhaphp-upload'><input style=\"float: left;margin-right: 15px;\" type=\"text\" name=\"{$param['name']}\" value=\"{$value}\"  placeholder=\"请上传或者填写资源连接\" autocomplete=\"off\" class=\"layui-input\">                    
                        <button style='margin-bottom: 5px;' type=\"button\" class=\"layui-btn layui-btn-primary\" id=\"up_{$param['name']}\"><i class=\"layui-icon\">&#xe681;</i>{$param['bt_title']}</button></div>
                        <script>
                        layui.use('upload', function(){
                                var load;
                                var upload = layui.upload;
                                  var uploadInst = upload.render({
                                    elem: '#up_{$param['name']}'
                                    ,url: \"$uploadUrl\"
                                    ,ext: 'mp3|wma|wav|amr|rm|rmvb|wmv|avi|mpg|mpeg|mp4'
                                    ,field:'media'
                                    ,accept: 'file'
                                     ,before: function(input){
                                          load = layer.load();
                                     }
                                    ,done: function(res){
                                          if(res.code==0){                                    
                                                layer.close(load);
                                                $('input[name=\"{$param['name']}\"]').val(res.data.src)                                         
                                            }else{
                                                 layer.close(load);
                                                layer.alert(res.msg);
                                            }
                                    }
                                    ,error: function(){
                                      //请求异常回调
                                    }
                                  });               
                            });
                        </script>";
                break;
            case 'voice':
                if (isset($param['value'])) {
                    $value = $param['value'];
                } else {
                    $value = '';
                }
                $uploadUrl = url('mp/Upload/uploadMedia');
                echo "<div class='rhaphp-upload'><input style=\"float: left;margin-right: 15px;\" type=\"text\" name=\"{$param['name']}\" value=\"{$value}\"  placeholder=\"请上传或者填写资源连接\" autocomplete=\"off\" class=\"layui-input\">
                        <button style='margin-right: 5px;' type=\"button\" class=\"layui-btn layui-btn-primary\" id=\"up_{$param['name']}\"><i class=\"layui-icon\">&#xe681;</i>{$param['bt_title']}</button>";
                if (isset($param['material'])) {
                    echo "<span onclick=\"getMaterial('{$param['name']}','voice')\" class=\"layui-btn layui-btn-primary\"><i class=\"layui-icon\">&#xe654;</i>选择素材</span>";
                }
                echo "
                        <script>
                         layui.use('upload', function(){
                                var load;
                                var upload = layui.upload;
                                  var uploadInst = upload.render({
                                    elem: '#up_{$param['name']}'
                                    ,url: \"$uploadUrl\"
                                    ,ext: 'mp3|wma|wav|amr|rm|rmvb|wmv|avi|mpg|mpeg|mp4'
                                    ,field:'media'
                                    ,accept: 'file'
                                     ,before: function(input){
                                          load = layer.load();
                                     }
                                    ,done: function(res){
                                          if(res.code==0){                                    
                                                layer.close(load);
                                                $('input[name=\"{$param['name']}\"]').val(res.data.src)                                         
                                            }else{
                                                 layer.close(load);
                                                layer.alert(res.msg);
                                            }
                                    }
                                    ,error: function(){
                                      //请求异常回调
                                    }
                                  });               
                            });
                        </script></div>";
                break;
            case 'video':
                if (isset($param['value'])) {
                    $value = $param['value'];
                } else {
                    $value = '';
                }
                $uploadUrl = url('mp/Upload/uploadMedia');
                echo "<div class='rhaphp-upload'><input style=\"float: left;margin-right: 15px;\" type=\"text\" name=\"{$param['name']}\" value=\"{$value}\"  placeholder=\"请上传或者填写资源连接\" autocomplete=\"off\" class=\"layui-input\">
                       <button style='margin-right: 5px;' type=\"button\" class=\"layui-btn layui-btn-primary\" id=\"up_{$param['name']}\"><i class=\"layui-icon\">&#xe681;</i>{$param['bt_title']}</button>";
                if (isset($param['material'])) {
                    echo "<span onclick=\"getMaterial('{$param['name']}','video')\" class=\"layui-btn layui-btn-primary\"><i class=\"layui-icon\">&#xe654;</i>选择素材</span>";
                }
                echo "
                        <script>
                        layui.use('upload', function(){
                                var load;
                                var upload = layui.upload;
                                  var uploadInst = upload.render({
                                    elem: '#up_{$param['name']}'
                                    ,url: \"$uploadUrl\"
                                    ,ext: 'mp3|wma|wav|amr|rm|rmvb|wmv|avi|mpg|mpeg|mp4'
                                    ,field:'media'
                                    ,accept: 'file'
                                     ,before: function(input){
                                          load = layer.load();
                                     }
                                    ,done: function(res){
                                          if(res.code==0){                                    
                                                layer.close(load);
                                                $('input[name=\"{$param['name']}\"]').val(res.data.src)                                         
                                            }else{
                                                 layer.close(load);
                                                layer.alert(res.msg);
                                            }
                                    }
                                    ,error: function(){
                                      //请求异常回调
                                    }
                                  });               
                            });
                        </script></div>";
                break;
            case 'file_mp':
                if (isset($param['value'])) {
                    $value = $param['value'];
                } else {
                    $value = '';
                }
                $uploadUrl = url('mp/Upload/uploadFileBYmpVerify');
                echo "<div class='rhaphp-upload'><input  style=\"width: 80%;float: left;margin-right: 15px;\" type=\"text\" name=\"{$param['name']}\" value=\"{$value}\"  placeholder=\"\" autocomplete=\"off\" class=\"layui-input\">
                        <button type=\"button\" class=\"layui-btn layui-btn-primary\" id=\"up_{$param['name']}\"><i class=\"layui-icon\">&#xe681;</i>{$param['bt_title']}</button>
                     
                        <script>
                        layui.use('upload', function(){
                                var load;
                                var upload = layui.upload;
                                  var uploadInst = upload.render({
                                    elem: '#up_{$param['name']}'
                                    ,url: \"$uploadUrl\"
                                    ,ext: 'mp3|wma|wav|amr|rm|rmvb|wmv|avi|mpg|mpeg|mp4|zip|rar'
                                    ,field:'file'
                                    ,accept: 'file'
                                     ,before: function(input){
                                          load = layer.load();
                                     }
                                    ,done: function(res){
                                          if(res.code==0){                                    
                                                layer.close(load);
                                                $('input[name=\"{$param['name']}\"]').val(res.data.src)                                         
                                            }else{
                                                 layer.close(load);
                                                layer.alert(res.msg);
                                            }
                                    }
                                    ,error: function(){
                                      //请求异常回调
                                    }
                                  });               
                            });
                        </script></div>";
                break;
            case 'file':
                if (isset($param['value'])) {
                    $value = $param['value'];
                } else {
                    $value = '';
                }
                $uploadUrl = url('mp/Upload/uploadFile');
                echo "<div class='rhaphp-upload'><input  style=\"width: 80%;float: left;margin-right: 15px;\" type=\"text\" name=\"{$param['name']}\" value=\"{$value}\"  placeholder=\"\" autocomplete=\"off\" class=\"layui-input\">
                        <button type=\"button\" class=\"layui-btn layui-btn-primary\" id=\"up_{$param['name']}\"><i class=\"layui-icon\">&#xe681;</i>{$param['bt_title']}</button>
                     
                        <script>
                        layui.use('upload', function(){
                                var load;
                                var upload = layui.upload;
                                  var uploadInst = upload.render({
                                    elem: '#up_{$param['name']}'
                                    ,url: \"$uploadUrl\"
                                    ,ext: 'mp3|wma|wav|amr|rm|rmvb|wmv|avi|mpg|mpeg|mp4|zip|rar|txt'
                                    ,field:'media'
                                    ,accept: 'file'
                                     ,before: function(input){
                                          load = layer.load();
                                     }
                                    ,done: function(res){
                                          if(res.code==0){                                    
                                                layer.close(load);
                                                $('input[name=\"{$param['name']}\"]').val(res.data.src)                                         
                                            }else{
                                                 layer.close(load);
                                                layer.alert(res.msg);
                                            }
                                    }
                                    ,error: function(){
                                      //请求异常回调
                                    }
                                  });               
                            });
                        </script></div>";
                break;
        }
    }

}