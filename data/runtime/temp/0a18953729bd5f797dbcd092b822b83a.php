<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:56:"/Users/ima/GitHub/rhaphp/themes/pc/mp/friends/index.html";i:1505625608;s:63:"/Users/ima/GitHub/rhaphp/themes/pc/mp/../admin/common/base.html";i:1505801784;}*/ ?>
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
        
<link rel="stylesheet" type="text/css" href="/public/static/mp/css/custom_menu.css" />
<form action="" class="layui-form" method="get">
<div class="layui-form-item">
    <div class="layui-inline">
            <label class="layui-form-label">呢称：</label>
            <div class="layui-input-block">
                <input type="text" name="nickname" value="<?php echo $post['nickname']; ?>" placeholder="请输入呢称" autocomplete="off" class="layui-input">
            </div>
    </div>
    <div class="layui-inline">
        <label class="layui-form-label">关注时间</label>
        <div class="layui-input-inline">
            <input name="times" id="rhaphp-time1" value="<?php echo $post['times']; ?>" class="layui-input" placeholder="开始时间 到 结束时间" lay-key="17" type="text">
        </div>
    </div>

    <div class="layui-inline">
        <label class="layui-form-label">互动时间</label>
        <div class="layui-input-block">
            <input  name="need" <?php if($post['need'] == '1'): ?> checked <?php endif; ?> lay-skin="primary" value="1" title="有效"  type="checkbox">
        </div>
    </div>
    <div class="layui-inline">
        <label class="layui-form-label">性别：</label>
        <div class="layui-input-block">
            <input name="sex" <?php if($post['sex'] == '1'): ?> checked <?php endif; ?> value="1" title="男" checked="" type="radio">
            <input name="sex" <?php if($post['sex'] == '2'): ?> checked <?php endif; ?> value="2" title="女" type="radio">
            <input name="sex" <?php if($post['sex'] == '0'): ?> checked <?php endif; ?> value="0" title="不限" type="radio">
        </div>
    </div>
    <div class="layui-inline">
        <div class="layui-input-block">
            <button type="submit" class="layui-btn layui-btn-normal">
                <i class="layui-icon">&#xe615;</i>
                搜索</button>
        </div>
    </div>
</div>
</form>
<form class="layui-form" action="" style="padding: 0px 10px 0px 10px;">
<table class="layui-table" lay-skin="line">
    <colgroup>
        <col width="50">
        <col width="80">
        <col>
    </colgroup>
    <thead>
    <!--<tr>-->
        <!--<th>&nbsp;全部</th>-->
        <!--<th></th>-->
        <!--<th></th>-->
        <!--<th></th>-->
        <!--<th></th>-->
        <!--<th></th>-->
        <!--<th></th>-->
        <!--<th></th>-->
    <!--</tr>-->
    <tr>
        <th><input name="" lay-skin="primary" lay-filter="allChoose" type="checkbox"></th>
        <th></th>
        <th><a id="synselect" class="layui-btn layui-btn-small" href="javascript:;">同步选中数丝信息</a><a id="getAllFriend" class="layui-btn layui-btn-small" href="javascript:;">同步全部数丝</a></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php if(is_array($friendList) || $friendList instanceof \think\Collection || $friendList instanceof \think\Paginator): $i = 0; $__LIST__ = $friendList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
    <tr>
        <td><input name="openid" value="<?php echo $v['openid']; ?>" lay-skin="primary" type="checkbox"></td>
        <td>
            <div  style="padding: 5px; border: #e6e6e6 solid 1px; width:35px; float: left; ">
                <img class="form_logo" src="<?php echo $v['headimgurl']; ?>" width="35" height="35">
            </div>
        </td>
        <td><?php echo $v['nickname']; ?></td>
        <td>关注：<?php echo date("Y-m-d",$v['subscribe_time']); ?></td>
        <td>
        <td></td>
        <td></td>
        <td><a class="rha-bt-a" href="<?php echo url('Message/replyMsg',['openid'=>$v['openid']]); ?>">发送消息</a></td>
    </tr>
    <?php endforeach; endif; else: echo "" ;endif; ?>
    </tbody>
</table>
</form>
<?php echo $friendList->render(); ?>
<script>
    layui.use('laydate', function() {
        var laydate = layui.laydate;
        laydate.render({
            elem: '#rhaphp-time1'
            ,type: 'datetime'
            ,range: '到'
            ,format: 'yyyy-M-d'
        });
    })
    layui.use('form', function(){
        var $ = layui.jquery, form = layui.form;

        //全选
        form.on('checkbox(allChoose)', function(data){
            var child = $(data.elem).parents('table').find('tbody input[type="checkbox"]');
            child.each(function(index, item){
                item.checked = data.elem.checked;
            });
            form.render('checkbox');
        });
    });
    layui.use('layer', function(){
        var $ = layui.jquery, layer = layui.layer;
        var  lastOpenid='';
        var page='1';
        $(function () {

            $('#getAllFriend').click(function () {
                //send(lastOpenid,page);
                layer.open({
                    type: 2,
                    title: '同步全部粉丝',
                    shadeClose: true,
                    shade: 0.5,
                    area: ['680px', '200px'],
                    content: '<?php echo url("mp/Friends/SynFriends"); ?>',
                    cancel: function(index, layero){
                        window.location.reload();
                    }
                });
            });
            //这里有问题的js处理逻辑，后期记得更改
            function send (lastOpenid,page){
                $.post("<?php echo url('mp/Friends/SynFriends'); ?>",{'lastOpenid':lastOpenid,'page':page},function (result) {
                    console.log(result);
                    if(result.status==1){
                        layer.msg('正在同步第'+result.page+'页', {
                            icon: 16
                            ,shade: 0.01,
                            time:0,
                        });
                        send(result.lastOpenid,result.page)
                    }
                    if(result.status==2){
                      //  layer.close(index);
                        layer.alert('同步完成', {
                            skin: 'layui-layer-lan'
                            ,closeBtn: 0
                        });
                        location.reload()

                    }
                    if(result.status==0){
                        layer.alert(result.msg, {
                            skin: 'layui-layer-lan'
                            ,closeBtn: 0
                        });
                    }

                })
            }
            $('#synselect').click(function () {
               var openids=[];
                $("input[name='openid']:checked").each(function (key,value) {
                    openids[key]=$(this).val();
                })
                $.post("<?php echo url('mp/Friends/SynSelect'); ?>",{'openids':openids},function (result) {
                    layer.alert(result.msg, {
                        closeBtn: 0
                    });
                })
            })
        })
    });
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