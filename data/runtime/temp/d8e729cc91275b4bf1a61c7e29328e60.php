<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:58:"/Users/ima/GitHub/rhaphp/themes/pc/admin/system/login.html";i:1505801913;}*/ ?>
<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <title>登录</title>
    <link rel="stylesheet" type="text/css" href="__STATIC__/admin/css/admin_base.css" />
    <link rel="stylesheet" type="text/css" href="__STATIC__/layui/css/layui.css" />
    <link rel="stylesheet" type="text/css" href="__STATIC__/admin/css/style.css" />
    <script type="text/javascript" src="__STATIC__/jquery.js"></script>
    <script type="text/javascript" src="__STATIC__/layui/layui.js"></script>
</head>
<body>
<!-- 用户登录 -->
<style>
    .layui-form-item .layui-input-inline {
        margin: 0 0 10px 0px
    }
</style>
<div class="login-main" id="login">
    <header class="layui-elip">后台登录</header>
    <form class="layui-form" id="loginform">
        <div class="layui-form-item">
            <div class="layui-input-inline">
                <input name="user_name" lay-verify="required" placeholder="请输入登录用户名"  type="text" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-inline">
                <input name="password" lay-verify="required" placeholder="请输入登录密码"  type="password" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-input-inline login-btn">
            <button type="submit" lay-submit="" lay-filter="login" class="layui-btn">登录</button>
        </div>
    </form>
</div>
<div class="footer" style="position: fixed;bottom: 0;left: 0;right: 0;">
    <div class="wrap">
        <!--请遵守安装使用协议，未经允许不得删除或者屏蔽有关RhaPHP字样-->
        <a href="http://www.rhaphp.com" target="_blank">官方社区</a>
        <i class="vs">|</i>
        Powered By RhaPHP 二哈系统 Copyright © 2017 All Rights Reserved.
    </div>
</div>
<script>
    layui.use('form', function(){
        var form = layui.form;
        form.on('submit(login)', function(data){
            $.post(
                "<?php echo url('Login/index'); ?>",
                data.field,
                function(obj) {
                    if(obj.code == 200){
                        layer.msg(obj.msg,{icon:1,time:2000},function () {
                            location.href="<?php echo url('mp/Mp/index'); ?>";
                        });
                    }else {
                        layer.alert(obj.msg);
                    }
                },
                "json"
            );
            return false;
        });
    });
</script>
</body>
</html>
