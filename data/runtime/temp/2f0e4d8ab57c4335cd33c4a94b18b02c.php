<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:55:"/Users/ima/GitHub/rhaphp/themes/pc/admin/app/index.html";i:1505654863;s:57:"/Users/ima/GitHub/rhaphp/themes/pc/admin/common/base.html";i:1505801784;}*/ ?>
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
        
<div style="padding: 0px 10px 0px 10px;">
<div class="layui-tab">
    <ul class="layui-tab-title">
        <li <?php if($type == 'index'): ?>class="layui-this"<?php endif; ?>> <a href="<?php echo url('index'); ?>">在使用</a></li>
        <li <?php if($type == 'uninstall'): ?>class="layui-this"<?php endif; ?>> <a href="<?php echo url('index',['type'=>'uninstall']); ?>">卸载应用</a></li>
        <li <?php if($type == 'notinstall'): ?>class="layui-this"<?php endif; ?>><a href="<?php echo url('index',['type'=>'notinstall']); ?>">安装应用</a></li>
    </ul>
</div>
<?php switch($type): case "index": ?>
<table class="layui-table" lay-even lay-skin="nob">
    <thead>
    <tr>
        <th>应用Logo</th>
        <th>应用名称</th>
        <th>应用标识</th>
        <th>简介</th>
        <th>版本</th>
        <th>新版本</th>
        <th>作者</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php if(is_array($addons) || $addons instanceof \think\Collection || $addons instanceof \think\Paginator): $i = 0; $__LIST__ = $addons;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
    <tr>
        <td><img width="30" height="30" src="<?php echo $vo['logo']; ?>"></td>
        <td><?php echo $vo['name']; ?></td>
        <td><?php echo $vo['addon']; ?></td>
        <td><?php echo $vo['desc']; ?></td>
        <td><?php echo $vo['version']; ?></td>
        <td><?php echo getAddonConfigByFile($vo['addon'],'version'); ?></td>
        <td><?php echo $vo['author']; ?></td>
        <td>
            <a class="layui-btn layui-btn-mini layui-btn-normal" href="<?php echo url('mp/App/index',['name'=>$vo['addon'],'type'=>'news','mid'=>$mpInfo['id']]); ?>">进入</a>
        </td>
    </tr>
    <?php endforeach; endif; else: echo "" ;endif; ?>
    </tbody>
</table>
<?php break; case "uninstall": ?>
    <table class="layui-table" lay-even lay-skin="nob">
        <thead>
        <tr>
            <th>应用Logo</th>
            <th>应用名称</th>
            <th>应用标识</th>
            <th>简介</th>
            <th>版本</th>
            <th>新版本</th>
            <th>作者</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if(is_array($addons) || $addons instanceof \think\Collection || $addons instanceof \think\Paginator): $i = 0; $__LIST__ = $addons;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
        <tr>
            <td><img width="30" height="30" src="<?php echo $vo['logo']; ?>"></td>
            <td><?php echo $vo['name']; ?></td>
            <td><?php echo $vo['addon']; ?></td>
            <td><?php echo $vo['desc']; ?></td>
            <td><?php echo $vo['version']; ?></td>
            <td><?php echo getAddonConfigByFile($vo['addon'],'version'); ?></td>
            <td><?php echo $vo['author']; ?></td>
            <td>
                <button class="layui-btn layui-btn-mini layui-btn-danger" onclick="closeApp('<?php echo $vo['addon']; ?>')">卸载</button>
            </td>
        </tr>
        <?php endforeach; endif; else: echo "" ;endif; ?>
        </tbody>
    </table>
    <?php break; case "notinstall": ?>
<table class="layui-table" lay-even lay-skin="nob">
    <thead>
    <tr>
        <th>应用Logo</th>
        <th>应用名称</th>
        <th>应用标识</th>
        <th>简介</th>
        <th>版本</th>
        <th>新版本</th>
        <th>作者</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php if(is_array($addons) || $addons instanceof \think\Collection || $addons instanceof \think\Paginator): $i = 0; $__LIST__ = $addons;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
    <tr >
        <td><img width="30" height="30" src="<?php echo getAddonLogo($vo['addon']); ?>"></td>
        <td><?php echo $vo['name']; ?></td>
        <td><?php echo $vo['addon']; ?></td>
        <td><?php echo $vo['desc']; ?></td>
        <td><?php echo $vo['version']; ?></td>
        <td><?php echo getAddonConfigByFile($vo['addon']); ?></td>
        <td><?php echo $vo['author']; ?></td>
        <td><button class="layui-btn layui-btn-mini " onclick="installApp('<?php echo $vo['addon']; ?>')">安装应用</button> </td>
    </tr>
    <?php endforeach; endif; else: echo "" ;endif; ?>
    </tbody>
</table>
<?php break; endswitch; ?>
</div>
<script>
    function installApp(name) {
        layui.use('layer', function(){
            var layer = layui.layer;
            $.post("<?php echo url('admin/app/install'); ?>",{'name':name},function (res) {
                if(res.status=='0'){
                    layer.alert(res.msg);
                }
                if(res.status=='1'){
                    layer.msg(res.msg,{time:1000},function () {
                        location.href="<?php echo url('admin/app/index',['type'=>'notinstall']); ?>";
                    });
                }
            })
        });
    }
    function closeApp(name) {
        layui.use('layer', function(){
            var layer = layui.layer;
            $.post("<?php echo url('admin/app/close'); ?>",{'name':name},function (res) {
                if(res.status=='0'){
                    layer.alert(res.msg);
                }
                if(res.status=='1'){
                    layer.msg(res.msg,{time:1000},function () {
                        location.href="<?php echo url('admin/app/index',['type'=>'uninstall']); ?>";
                    });
                }
            })
        });
    }
</script>

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