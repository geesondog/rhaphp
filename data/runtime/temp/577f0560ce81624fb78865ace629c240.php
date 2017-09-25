<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:57:"/Users/ima/GitHub/rhaphp/themes/pc/mp/material/index.html";i:1505625608;s:63:"/Users/ima/GitHub/rhaphp/themes/pc/mp/../admin/common/base.html";i:1505801784;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <meta name="keywords" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <title>RhaPHP - 二哈公众号管理系统</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" type="text/css" href="__STATIC__/admin/css/admin_base.css" />
    <script type="text/javascript" src="__STATIC__/jquery/jquery-1.11.0.min.js"></script>
    <script type="text/javascript" src="__STATIC__/layui/layui.js"></script>
    <link rel="stylesheet" type="text/css" href="__STATIC__/layui/css/layui.css" />
    <link rel="stylesheet" type="text/css" href="__STATIC__/icon/icon.css" />
    
</head>
<body class="trade-order">
<div class="topbar" id="gotop">
    <div class="wrap">
        <ul>
            <li>你好，<a class="name" href="" id="username"><?php echo $admin['admin_name']; ?></a>
                <?php if(!(empty($mpInfo) || (($mpInfo instanceof \think\Collection || $mpInfo instanceof \think\Paginator ) && $mpInfo->isEmpty()))): ?>
                <span class="quit">当前公众号：<a href="<?php echo url('mp/index/index',['mid'=>$mpInfo['id']]); ?>"><?php echo $mpInfo['name']; ?></a><i style="font-size: 9px; margin-left: 5px;"><?php echo getMpType($mpInfo['type']); ?></i>
                    <?php if($mpInfo['valid_status'] == '1'): ?>
                    <i style="font-size: 9px; margin-left: 5px;">已接入</i>
                    <?php else: ?>
                    <i style="font-size: 9px; margin-left: 5px; color: red">未接入</i>
                    <?php endif; ?>
                </span>
                <a class="quit" href="<?php echo url('mp/index/mplist'); ?>">切换公众号</a>
                <?php endif; ?>

                <a class="quit" href="<?php echo url('admin/Login/out'); ?>"><i class="rha-icon">&#xe696;</i>退出</a>
            </li>
            <li>
                <a href="<?php echo url('mp/Message/messagelist'); ?>"><i class="layui-icon">&#xe645;</i>用户消息<span class="num-feed rhaphp-msg-user show" style="display: none;">0</span></a>
            </li>

        </ul>
    </div>
</div>
<div class="header">
    <div class="wrap">
        <div class="logo">
            <h1 class="main-logo"><a href="<?php echo url('mp/mp/index'); ?>">RhaPHP</a></h1>
            <div class="sub-logo"></div>
        </div>
        <div class="nav">
            <ul>
                <?php if(is_array($t_menu) || $t_menu instanceof \think\Collection || $t_menu instanceof \think\Paginator): $i = 0; $__LIST__ = $t_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$t): $mod = ($i % 2 );++$i;?>
                <li class="<?php if($topNode == $t['url']): ?>selected<?php endif; ?>"><a href="<?php echo url($t['url']); ?>"><?php echo $t['name']; ?></a></li>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        </div>
    </div>
</div>
<div class="container_body wrap">
    <div class="sidebar">
        <div class="menu">
            <?php if(is_array($menu) || $menu instanceof \think\Collection || $menu instanceof \think\Paginator): $i = 0; $__LIST__ = $menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$t): $mod = ($i % 2 );++$i;?>
            <dl>
                <dt><i class="type-ico ico-trade rha-icon <?php if($t['shows'] == '1'): endif; ?>"><?php echo $t['icon']; ?></i><?php echo $t['name']; ?></dt>
                <?php if(is_array($t['child']) || $t['child'] instanceof \think\Collection || $t['child'] instanceof \think\Paginator): $i = 0; $__LIST__ = $t['child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$c): $mod = ($i % 2 );++$i;?>
                <dd class="<?php if($c['shows'] == '1'): ?>selected<?php endif; ?>"><a href="<?php echo url($c['url']); ?>"><?php echo $c['name']; ?></a></dd>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </dl>
            <?php endforeach; endif; else: echo "" ;endif; ?>
            
            <dl>
                <?php  if(!isset($menu_app))$menu_app=null; if($menu_app != ''): ?><dt><i class="type-ico ico-trade rha-icon">&#xe6f0;</i>应用扩展</dt><?php endif; if(is_array($menu_app) || $menu_app instanceof \think\Collection || $menu_app instanceof \think\Paginator): $i = 0; $__LIST__ = $menu_app;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                <dd class=""><a href="<?php echo url('mp/App/index',['name'=>$v['addon'],'type'=>'news','mid'=>$mid]); ?>"><?php echo $v['name']; ?></a></dd>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </dl>
        </div>
    </div>
    <div class="content" id="tradeSearchBd">
        <?php if(isset($menu_tile) OR $menu_title != ''): ?>
        <div class="content-hd">
            <h2><?php echo $menu_title; ?></h2>
        </div>
        <?php endif; ?>
        
<div style="padding: 0px 10px 0px 10px">
    <div class="layui-btn-group" style=" margin-bottom: 5px;">
        <button onclick="sycMaterial('image')" class="layui-btn layui-btn-small"><i class="layui-icon">&#x1002;</i>同步图片</button>
        <button onclick="sycMaterial('news')" class="layui-btn layui-btn-normal layui-btn-small "><i class="layui-icon">&#x1002;</i>同步图文</button>
        <button onclick="sycMaterial('voice')" class="layui-btn layui-btn-normal layui-btn-small "><i class="layui-icon">&#x1002;</i>同步语音</button>
        <button onclick="sycMaterial('video')" class="layui-btn layui-btn-warm layui-btn-small"><i class="layui-icon">&#x1002;</i>同步视频</button>
    </div>
    <div class="layui-tab">
        <ul class="layui-tab-title">
            <li <?php if($fron_type == '1'): ?>class="layui-this"<?php endif; ?>><a href="<?php echo url('mp/Material/index',['type'=>'image','from_type'=>1]); ?>"> 微信服务器</a></li>
            <!--<li <?php if($fron_type == '0'): ?>class="layui-this"<?php endif; ?>><a href="<?php echo url('mp/Material/index',['type'=>'image','from_type'=>0]); ?>">本地素材</a></li>-->
        </ul>
    </div>
</div>
<div class="layui-tab-content">
    <div class="layui-tab-item <?php if($fron_type == '1'): ?>layui-show<?php endif; ?>">


    <div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">
        <ul class="layui-tab-title">
            <li <?php if($type == 'image'): ?>class="layui-this"<?php endif; ?>><a href="<?php echo url('mp/Material/index',['type'=>'image','from_type'=>1]); ?>">图片</a></li>
            <li <?php if($type == 'news'): ?>class="layui-this"<?php endif; ?>><a href="<?php echo url('mp/Material/index',['type'=>'news','from_type'=>1]); ?>">图文</a></li>
            <li <?php if($type == 'voice'): ?>class="layui-this"<?php endif; ?>><a href="<?php echo url('mp/Material/index',['type'=>'voice','from_type'=>1]); ?>">语音</a></li>
            <li <?php if($type == 'video'): ?>class="layui-this"<?php endif; ?>><a href="<?php echo url('mp/Material/index',['type'=>'video','from_type'=>1]); ?>">视频</a></li>
        </ul>
        <div class="layui-tab-content">
            <?php switch($type): case "news": ?>
            <ul class="type_image">
                <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                <li class="material_li">
                    <div class="material_li_box">
                        <?php if(is_array($v['news_item']) || $v['news_item'] instanceof \think\Collection || $v['news_item'] instanceof \think\Paginator): $i = 0; $__LIST__ = $v['news_item'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$news): $mod = ($i % 2 );++$i;if($key == '0'): ?>
                        <div class="material_li_t">
                            <p><?php echo $news['title']; ?></p>
                            <img width="169" height="140" src="<?php echo $news['thumb_url']; ?>">
                        </div>
                        <?php else: ?>
                        <div class="material_li_b">
                            <img width="30" height="30" src="<?php echo $news['thumb_url']; ?>">
                            <p><?php echo $news['title']; ?></p>
                        </div>
                        <?php endif; endforeach; endif; else: echo "" ;endif; ?>
                    </div>
                    <div>
                        <div class="layui-btn-group">
                            <button onclick="sendMaterial('<?php echo $v['media_id']; ?>','image')" style="width: 50%" class="layui-btn layui-btn-primary layui-btn-small">
                                <i class="layui-icon">&#xe609;</i>群发
                            </button>
                            <button onclick="delMaterial('<?php echo $v['media_id']; ?>','image')" style="width: 50%;" class="layui-btn layui-btn-primary layui-btn-small">
                                <i class="layui-icon">&#xe640;</i>删除
                            </button>
                        </div>
                    </div>
                </li>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
            <?php echo $page; break; case "voice": ?>
            <table class="layui-table" lay-skin="line">
                <thead>
                <tr>
                    <th>语音名称</th>
                    <th>媒体 ID</th>
                    <th>创建时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                <tr>
                    <td><?php echo $vo['title']; ?></td>
                    <td><?php echo $vo['media_id']; ?></td>
                    <td><?php echo $vo['create_time']; ?></td>
                    <td>
                        <div class="">
                            <button onclick="delMaterial('<?php echo $vo['media_id']; ?>','voice')" class="layui-btn layui-btn-mini layui-btn-danger">
                                <i class="layui-icon">&#xe640;删除</i>
                            </button>
                        </div>

                    </td>
                </tr>
                <?php endforeach; endif; else: echo "" ;endif; ?>
                </tbody>
            </table>
            <?php echo $data->render(); break; case "image": ?>
            <ul class="type_image">
                <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                <li class="material_li">
                    <span><img src="<?php echo $v['url']; ?>" width="169" height="169"></span>
                    <span>
                                <div class="layui-btn-group">
                                  <button onclick="sendMaterial('<?php echo $v['media_id']; ?>','image')" style="width: 50%" class="layui-btn layui-btn-primary layui-btn-small">
                                    <i class="layui-icon">&#xe609;</i>群发
                                  </button>
                                  <button onclick="delMaterial('<?php echo $v['media_id']; ?>','image')" style="width: 50%;" class="layui-btn layui-btn-primary layui-btn-small">
                                    <i class="layui-icon">&#xe640;</i>删除
                                  </button>
                                </div>
                            </span>
                </li>

                <?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
            <?php echo $data->render(); break; case "video": ?>
            <table class="layui-table" lay-skin="line">
                <thead>
                <tr>
                    <th>视频名称</th>
                    <th>媒体 ID</th>
                    <th>创建时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                <tr>
                    <td><?php echo $vo['title']; ?></td>
                    <td><?php echo $vo['media_id']; ?></td>
                    <td><?php echo $vo['create_time']; ?></td>
                    <td>
                        <div class="">
                            <button onclick="delMaterial('<?php echo $vo['media_id']; ?>','video')" class="layui-btn layui-btn-mini layui-btn-danger">
                                <i class="layui-icon">&#xe640;删除</i>
                            </button>
                        </div>

                    </td>
                </tr>
                <?php endforeach; endif; else: echo "" ;endif; ?>
                </tbody>
            </table>
            <?php echo $data->render(); break; endswitch; ?>
        </div>
    </div>

</div>
<div class="layui-tab-item <?php if($fron_type == '0'): ?>layui-show<?php endif; ?>"><!--内容2--></div>
</div>
<script>
    function sendMaterial(media_id,type) {
        layui.use('layer', function(){
            var layer = layui.layer;
            layer.confirm('你确定需要群发吗？认证号可用、订阅号一天1条，服务号一个月四条。', {
                btn: ['是','不'] //按钮
            }, function(){
                $.post("<?php echo url('mp/Material/sendMaterial'); ?>",{'media_id':media_id,'type':type},function (res) {
                    if(res.status==1){
                        layer.alert(res.msg);
                    }else{
                        layer.alert(res.msg);
                    }

                })
            }, function(){

            });
        });

    }
    function delMaterial(media_id,type) {
        layui.use('layer', function(){
            var layer = layui.layer;
            layer.confirm('你确定需要删除吗？', {
                btn: ['是','不'] //按钮
            }, function(){
                $.post("<?php echo url('mp/Material/delMaterial'); ?>",{'media_id':media_id,'type':type},function (res) {
                    if(res.status==1){
                        layer.alert(res.msg)
                    }else{
                        layer.alert(res.msg)
                    }
                })
            }, function(){

            });
        });

    }
    function sycMaterial(type) {
        layui.use('layer', function(){
            var layer = layui.layer;
            layer.open({
                type: 2,
                title: '正在同步',
                shadeClose: true,
                shade: 0.5,
                area: ['680px', '200px'],
                content: '<?php echo getHostDomain(); ?>/index.php/mp/Material/sycMaterial/type/'+type+'/offset/0',
                cancel: function(index, layero){
                    window.location.reload();
                }
            })
        });

    }
</script>

<style>

.content li.material_li{height:auto}

.content .type_image li{float:left;}

li.material_li .material_li_box .material_li_t{
    position: relative;
}

li.material_li .material_li_box .material_li_t img{
    display: block;
}

li.material_li .material_li_box .material_li_t p{
    color: #fff;
    position: absolute;
    background: rgba(0,0,0,0.7);
    bottom: 0px;
    left: 0px;
    right: 0px;
    padding: 4px;
    font-size: 12px;
    line-height: 16px;
}

.type_image .layui-btn-group{
    width: 100%;
}

.material_li_b{
    border-top: 1px solid #eee;
    padding-top: 4px;
    padding-bottom: 4px;
}

.material_li_b img{float: right; margin: 5px;}
.material_li_b p{
    padding-left: 5px;
    font-size: 12px;
}

</style>


    </div>
</div>
<div class="footer">
    <div class="wrap">
        <!--请遵守安装使用协议，未经允许不得删除或者屏蔽有关RhaPHP字样-->
        <a href="http://www.rhaphp.com" target="_blank">官方社区</a>
        <i class="vs">|</i>
        Powered By RhaPHP<?php echo $copy['version']; ?> 二哈系统 Copyright © 2017 All Rights Reserved.
    </div>
</div>
</body>
<script>
    layui.use('element', function(){
        var element = layui.element;
    });
    function getMaterial(paramName,type){
        layer.open({
            type: 2,
            title: '选择素材',
            shadeClose: true,
            shade: 0.8,
            area: ['750px', '480px'],
            content: '<?php echo getHostDomain(); ?>/index.php/mp/Material/getMeterial/type/'+type+'/param/'+paramName //iframe的url
        });
    }
    function controllerByVal(value,paramName,type) {
        $('.form_'+paramName).attr('src',value);
        $("input[name="+paramName+"]").val(value);
    }
    $(function () {
         setInterval(getMsgTotal,10000);
        function getMsgTotal() {
            $.get("<?php echo url('mp/Message/getMsgStatusTotal'); ?>",{},function (res) {
                if(res.msgTotal==0){
                    //TODO
                }else{
                    $('.rhaphp-msg-user').show();
                    $('.rhaphp-msg-user').text(res.msgTotal);
                }

            })
        }
    })
</script>
</html>