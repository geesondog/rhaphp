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

class Upload {

    /**
     * @author geeson 314835050@qq.com
     * @param type : image 单图 images 多图
     * @param name INPUT name值
     * @param null $param
     */
    public function run($param = null) {
        if (ENTR_PATH == '') {
            $IMGpath = '/public';
        } else {
            $IMGpath = '';
        }
        $maxFileSize = 3072;
        if (isset($param['size'])) {
            $maxFileSize = $param['size'];
        }
        echo '<style>'
            . '.rhaphp-hook-upload-image{overflow: hidden;}'
            . '.upload-imgs{position: relative;border: 1px solid #e6e6e6; padding: 3px; display: inline-block; margin-right: 10px;}'
            . '.upload-imgs img {width: 150px; height: 90px;}'
            . '.upload-imgs-del{cursor: pointer;position: absolute;font-size: 12px;right: 3px;background-color: #FF5722;padding: 2px 5px;color: #fff;}'
            . '</style>';
        switch ($param['type']) {
            case 'image':
                $imagesPath = getHostDomain() . $IMGpath . '/static/images/def.jpg';//新增的定义
                $value = '';
                if (isset($param['value'])) {
                    if (!is_array($param['value'])) {
                        $imagesPath = $param['value'];//当传入值不为空，且不为数组的时候替换新增定义（大多数是在编辑图片的时候）
                    }
                    $value = $imagesPath;//当传入值不为空，且是一个图片地址，则给value赋值
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

                $deleteFileUrl = url("/mp/upload/deleteFile", "", "") . "/picId/'+picid"; //删除图片的地址

                echo "<div class='rhaphp-hook-upload-image'>
                            <div class='rhaphp-upload-thumb'  style=\"padding: 5px; border: #e6e6e6 solid 1px; float: left; \">
                                <img class=\"form_{$param['name']}\" data-picid=\"0\" src=\"{$imagesPath}\" width=\"150\" height=\"90\">
                            </div>
                            <div class='rhaphp-upload' style=\"margin-bottom: 10px; margin-left: 5px; float: left; \">
                                <input style=\"display: none\" type=\"text\" value=\"{$value}\" name=\"{$param['name']}\" >
                                <button style='margin-bottom: 5px;' type=\"button\" class=\"layui-btn layui-btn-primary\" id=\"up_{$param['name']}\"><i class=\"layui-icon\">&#xe681;</i>上传图片</button>
                                ";
                if (isset($param['material'])) {
                    $getMeterial_url = url("mp/Material/getMeterial", "", "") . "/type/'+type+'/param/'+paramName";
                    echo "<BR><span onclick=\"getMaterial('{$param['name']}','image')\" class=\"layui-btn layui-btn-primary btn-sc\"><i class=\"layui-icon\">&#xe654;</i>选择素材</span><script reload='1'>function getMaterial(paramName,type){layer.open({type: 2,title: '选择素材',shadeClose: true,shade: 0.1,area: ['750px', '480px'],
content: '{$getMeterial_url}
     })}
 function controllerByVal(value,paramName,type) {
        console.log('controllerByVal: '+value);
        console.log('controllerByVal: '+paramName);
        $('.form_'+paramName).attr('src',value);
        $('.form_'+paramName).data('picid',0);
        $(\"input[name=\"+paramName+\"]\").val(value);
    }
</script>";
                }
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
                                    ,size: \"{$maxFileSize}\"
                                    ,before: function(input){
                                          var picid = $('.form_{$param['name']}').data('picid');
                                          if(picid && picid > 0){
                                            //更新数据库，删除文件
                                            $.post('{$deleteFileUrl});
                                          }
                                          load = layer.load(1);
                                     }
                                    ,done: function(res){
                                      if(res.code==0){                                    
                                            $('input[name=\"{$param['name']}\"]').val(res.data.src)
                                            $('.form_{$param['name']}').attr('src',res.data.src);
                                            $('.form_{$param['name']}').data('picid',res.data.picId);
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
                $value = '';
                if (isset($param['value'])) {
                    $value = $param['value'];
                }
                $maxFileCount = 5;
                if (isset($param['number'])) {
                    $maxFileCount = $param['number'];
                }
                $html = '';
                if (is_array($value)) {
                    foreach ($value as $key => $val) {
                        $maxFileCount++; //原有的要加进来
                        $html .= '<div  class="upload-imgs"><img src=' . $val . '><span class="upload-imgs-del" onclick="delImg(this,0)">删除</span>';
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
                echo "<div class='rhaphp-hook-upload-image'>
                            <div class='upload-images-list'>
                                <div class='upload-images-list list_{$param['name']}'>
                                {$html}
                                </div>        
                            </div>
                            <div  class='rhaphp-upload' style=\"margin:5px 5px 0px 0px;; float: left; \">
                              
                                <button type=\"button\" class=\"layui-btn layui-btn-primary\" id=\"up_{$param['name']}\"><i class=\"layui-icon\">&#xe681;</i>多图上传</button>
                                ";
                $deleteFileUrl = url("/mp/upload/deleteFile", "", "") . "/picId/'+picId"; //删除图片的地址
                if (isset($param['material'])) {
                    $getMeterial_url = url("mp/Material/getMeterialByImages", "", "") . "/type/'+type+'/param/'+paramName";
                    echo "<span onclick=\"getMeterialByImages('{$param['name']}','image')\" class=\"layui-btn layui-btn-primary btn-sc\"><i class=\"layui-icon\">&#xe654;</i>选择素材</span><script>function getMeterialByImages(paramName,type){
                            layer.open({type: 2,title: '选择素材',shadeClose: true,shade: 0.1,area: ['750px', '480px'],
                            content: '{$getMeterial_url}
                                });
                            }</script>";
                }
                echo "
                    </div></div>
                    <script reload='1'>
                        var fileCount = 0;//控制文件数量
                        var maxFileCount = \"{$maxFileCount}\";//文件上传最大数量
                        function controllerByValByImages(value,paramName,type) {
                            fileCount++;
                            if(fileCount > maxFileCount){
                                fileCount--;                              
                                layer.msg('文件数量不得超过'+maxFileCount+'个',{icon:2});
                                return false;
                            }
                            $('.list_'+paramName).append('<div class=\"upload-imgs\"><img src=\"'+value+'\"><span class=\"upload-imgs-del\" onclick=\"delImg(this,0)\">删除</span><input name=\"'+paramName+'[]\" type=\"hidden\" value=\"'+value+'\"></div>');
                        }
                        layui.use('upload', function(){
                            var load;
                            var upload = layui.upload;
                              var uploadInst = upload.render({
                                elem: '#up_{$param['name']}'
                                ,url: \"$uploadUrl\"
                                ,accept: 'images' //普通文件
                                ,field:'image'
                                ,multiple:true
                                ,number: \"{$maxFileCount}\"
                                ,size: \"{$maxFileSize}\"
                                ,auto:false
                                ,choose: function(input){
                                    //将每次选择的文件追加到文件队列
                                    var files = input.pushFile();
                                    //预读本地文件，如果是多文件，则会遍历。(不支持ie8/9)
                                    input.preview(function(index, file, result){
                                      //这里还可以做一些 append 文件列表 DOM 的操作
                                      fileCount++;
                                      console.log('controllerByValByImages fileCount ' + fileCount);
                                      if(fileCount > maxFileCount){
                                        fileCount--;
                                        console.log('controllerByValByImages fileCount 2' + fileCount);
                                        layer.msg('文件数量不得超过'+maxFileCount+'个',{icon:2});
                                        delete files[index]; //删除列表中对应的文件，一般在某个事件中使用
                                        return false;
                                      }else{
                                        console.log('上传'); //
                                        input.upload(index, file); //上传
                                      }
                                    });
                                 }
                                 ,before: function(obj){ //obj参数包含的信息，跟 choose回调完全一致，可参见上文。
                                    layer.load(); //上传loading
                                  }
                                ,allDone: function(obj){ //当文件全部被提交后，才触发
                                    console.log(obj.total); //得到总文件数
                                  }
                                ,done: function(res, index, upload){
                                    console.log(res);
                                    if(res.code==0){
                                      $('.list_{$param['name']}').append('<div class=\"upload-imgs\"><img src='+res.data.src+'><span class=\"upload-imgs-del\" onclick=\"delImg(this,'+res.data.picId+')\">删除</span><input name=\"{$param['name']}['+res.data.picId+']\" type=\"hidden\" value='+res.data.src+'></div>');
                                        layer.closeAll('loading'); //关闭loading
                                    }else{
                                        fileCount--;
                                        layer.closeAll('loading'); //关闭loading
                                        layer.alert(res.msg);
                                    }
                                }
                              });               
                        });
                        function delImg(obj,picId) {
                            if(picId !== 0){
                                //更新数据库，删除文件
                                $.post('{$deleteFileUrl});
                            }
                            fileCount--;
                            console.log('delImg fileCount ' + fileCount);
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
                                    ,size: \"{$maxFileSize}\"
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
                                    ,size: \"{$maxFileSize}\"
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
                                    ,size: \"{$maxFileSize}\"
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
                                    ,size: \"{$maxFileSize}\"
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
                                    ,size: \"{$maxFileSize}\"
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
