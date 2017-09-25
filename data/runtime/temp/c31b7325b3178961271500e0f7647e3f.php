<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:50:"/Users/ima/GitHub/rhaphp/themes/pc/mp/mp/menu.html";i:1505625608;s:63:"/Users/ima/GitHub/rhaphp/themes/pc/mp/../admin/common/base.html";i:1505801784;}*/ ?>
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
    
<link rel="stylesheet" type="text/css" href="__STATIC__/admin/css/console.css" />

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
        
<style>
    .mobile-preview { -moz-user-select: none; -webkit-user-select: none; -ms-user-select: none; -khtml-user-select: none; user-select: none;}
    .menu-editor { left: 317px; display: block; max-width: 500px; width: 500px; height: 580px; border-radius: 0; border-color: #e7e7eb; box-shadow: none}
    .menu-editor .arrow { top: auto !important; bottom: 15px}
    .menu-editor .popover-title { margin-top: 0}
    .menu-delete { font-weight: 400; font-size: 12px;}
    .menu-submit { margin-right: 10px}
</style>
<div class="mp-menu">
    <div class='mobile-preview pull-left'>
        <div class='mobile-header'><?php echo $mpInfo['name']; ?></div>
        <div class='mobile-body'></div>
        <ul class='mobile-footer'>
            <?php foreach($list as $menu): ?>
            <li class="parent-menu">
                <a><i class="icon-sub hide"></i> <span data-type="<?php echo $menu['type']; ?>" data-content="<?php echo $menu['content']; ?>"><?php echo $menu['name']; ?></span></a>
                <div class="sub-menu text-center hide">
                    <ul>
                        <?php if(empty($menu['sub']) == false): foreach($menu['sub'] as $submenu): ?>
                        <li>
                            <a class="bottom-border"><span data-type="<?php echo $submenu['type']; ?>" data-content="<?php echo $submenu['content']; ?>"><?php echo $submenu['name']; ?></span></a>
                        </li>
                        <?php endforeach; endif; ?>
                        <li class="menu-add"><a><i class="icon-add"></i></a></li>
                    </ul>
                    <i class="arrow arrow_out"></i>
                    <i class="arrow arrow_in"></i>
                </div>
            </li>
            <?php endforeach; ?>
            <li class="parent-menu menu-add">
                <a><i class="icon-add"></i></a>
            </li>
        </ul>
    </div>
    <div class="pull-left" style="position:absolute">
        <div class="popover fade right up in menu-editor">
            <div class="arrow"></div>
            <h3 class="popover-title">
                菜单名称

                <a class="pull-right menu-delete">删除</a>

            </h3>
            <div class="popover-content menu-content"></div>
        </div>
    </div>
    <div class="hide menu-editor-parent-tpl">
        <form class="layui-form">
            <p>已添加子菜单，仅可设置菜单名称。</p>
            <div class="layui-form-item" style="margin-top:50px">
                <label class="layui-form-label">菜单名称</label>
                <div class="layui-input-block">
                    <input name="menu-name" class="layui-input">
                    <span class="layui-form-mid layui-word-aux">字数不超过5个汉字或16个字母</span>
                </div>
            </div>
        </form>
    </div>
    <div class="hide menu-editor-content-tpl layui-form">
        <form class="form-horizontal ">
            <div class="layui-form-item" style="margin-top:50px">
                <label class="layui-form-label">菜单名称</label>
                <div class="layui-input-block">
                    <input name="menu-name" class="layui-input">
                    <span class="layui-form-mid layui-word-aux">字数不超过13个汉字或40个字母</span>
                </div>
            </div>
            <div class="layui-form-item" style="margin-top:30px">
                <label class="layui-form-label">菜单内容</label>
                <div class="layui-input-block">
                        <!--<label class="col-xs-5 font-noraml">-->
                            <!--<input class="cuci-radio" type="radio" name="menu-type" value="text"> 文字消息-->
                        <!--</label>-->
                        <label class="col-xs-5 font-noraml">
                            <input class="cuci-radio" type="radio" name="menu-type" value="keys"> 关键字
                        </label>
                        <label class="col-xs-5 font-noraml">
                            <input class="cuci-radio" type="radio" name="menu-type" value="view"> 跳转网页
                        </label>
                        <label class="col-xs-5 font-noraml">
                            <input class="cuci-radio" type="radio" name="menu-type" value="event"> 事件功能
                        </label>
                        <label class="col-xs-5 font-noraml">
                            <input class="cuci-radio" type="radio" name="menu-type" value="miniprogram"> 小程序
                        </label>
                        <!--<label class="col-xs-5 font-noraml">-->
                            <!--<input class="cuci-radio" type="radio" name="menu-type" value="customservice"> 多客服-->
                        <!--</label>-->
                </div>
            </div>
            <div class="layui-form-item editor-content-input" style="margin-top:30px"></div>
        </form>
    </div>
    <div style="clear:both"></div>
    <div style="width:830px;padding-top:40px;text-align:center">
        <button class="layui-btn menu-submit" lay-filter="formDemo">保存发布</button>
        <button data-load='' class="layui-btn layui-btn-danger">取消发布</button>
    </div>
</div>
<script>
    $(function () {
        /**
         * 菜单事件构造方法
         * @returns {menu.index_L2.menu}
         */
        var menu = function () {
            this.version = '1.0';
            this.$btn;
            this.listen();
        };
        /**
         * 控件默认事件
         * @returns {undefined}
         */
        var layer;
        layui.use('layer', function(){
             layer = layui.layer;

        });
        menu.prototype.listen = function () {
                var self = this;
                $('.mobile-footer').on('click', 'li a', function () {
                    self.$btn = $(this);
                    self.$btn.parent('li').hasClass('menu-add') ? self.add() : self.checkShow();
                }).find('li:first a:first').trigger('click');
                $('.menu-delete').on('click', function () {
                    var index = layer.confirm('确定删除吗？', function () {
                        self.del(), layer.close(index);
                    });
                });
                $('.menu-submit').on('click', function () {
                    self.submit();
                });
        };
        /**
         * 添加一个菜单
         * @returns {undefined}
         */
        menu.prototype.add = function () {
            var $add = this.$btn.parent('li'), $ul = $add.parent('ul');
            if ($ul.hasClass('mobile-footer')) { /* 添加一级菜单 */
                var $li = $('<li class="parent-menu"><a class="active"><i class="icon-sub hide"></i> <span>一级菜单</span></a></li>').insertBefore($add);
                this.$btn = $li.find('a');
                $('<div class="sub-menu text-center hide"><ul><li class="menu-add"><a><i class="icon-add"></i></a></li></ul><i class="arrow arrow_out"></i><i class="arrow arrow_in"></i></div>').appendTo($li);
            } else { /* 添加二级菜单 */
                this.$btn = $('<li><a class="bottom-border"><span>二级菜单</span></a></li>').prependTo($ul).find('a');
            }
            this.checkShow();
        };
        /**
         * 数据校验显示
         * @returns {unresolved}
         */
        menu.prototype.checkShow = function () {
            var $li = this.$btn.parent('li'), $ul = $li.parent('ul');
            /* 选中一级菜单时显示二级菜单 */
            if ($li.hasClass('parent-menu')) {
                $('.parent-menu .sub-menu').not(this.$btn.parent('li').find('.sub-menu').removeClass('hide')).addClass('hide');
            }

            /* 一级菜单添加按钮 */
            var $add = $('li.parent-menu:last');
            $add.siblings('li').size() >= 3 ? $add.addClass('hide') : $add.removeClass('hide');
            /* 二级菜单添加按钮 */
            $add.siblings('li').map(function () {
                var $add = $(this).find('ul li:last');
                $add.siblings('li').size() >= 5 ? $add.addClass('hide') : $add.removeClass('hide');
            });
            /* 处理一级菜单 */
            var parentWidth = 100 / $('li.parent-menu:visible').size() + '%';
            $('li.parent-menu').map(function () {
                var $icon = $(this).find('.icon-sub');
                $(this).width(parentWidth).find('ul li').size() > 1 ? $icon.removeClass('hide') : $icon.addClass('hide');
            });
            /* 更新选择中状态 */
            $('.mobile-footer a.active').not(this.$btn.addClass('active')).removeClass('active');
            this.renderEdit();
            return $ul;
        };
        /**
         * 删除当前菜单
         * @returns {undefined}
         */
        menu.prototype.del = function () {
            var $li = this.$btn.parent('li'), $ul = $li.parent('ul');
            var $default = function () {
                if ($li.prev('li').size() > 0) {
                    return $li.prev('li');
                }
                if ($li.next('li').size() > 0 && !$li.next('li').hasClass('menu-add')) {
                    return $li.next('li');
                }
                if ($ul.parents('li.parent-menu').size() > 0) {
                    return $ul.parents('li.parent-menu');
                }
                return $('null');
            }.call(this);
            $li.remove();
            this.$btn = $default.find('a:first');
            this.checkShow();
        };
        /**
         * 显示当前菜单的属性值
         * @returns {undefined}
         */
        menu.prototype.renderEdit = function () {
            var $span = this.$btn.find('span'), $li = this.$btn.parent('li'), $ul = $li.parent('ul');
            var $html = '';
            if ($li.find('ul li').size() > 1) { /*父菜单*/
                $html = $($('.menu-editor-parent-tpl').html());
                $html.find('input[name="menu-name"]').val($span.text()).on('change keyup', function () {
                    $span.text(this.value || ' ');
                });
                $('.menu-editor .menu-content').html($html);
            } else {
                $html = $($('.menu-editor-content-tpl').html());
                $html.find('input[name="menu-name"]').val($span.text()).on('change keyup', function () {
                    $span.text(this.value || ' ');
                });
                $('.menu-editor .menu-content').html($html);
                var type = $span.attr('data-type') || 'text';
                $html.find('input[name="menu-type"]').on('click', function () {
                    $span.attr('data-type', this.value || 'text');
                    var content = $span.data('content') || '';
                    var type = this.value;
                    var html = function () {
                        switch (type) {
                            case 'miniprogram':
                                var tpl = '<div><div class="layui-form-item"><label class="layui-form-label">appid</label><div class="layui-input-block"><input type="text" name="appid" required="" lay-verify="required" placeholder="appid" autocomplete="off" class="layui-input" value="{appid}"></div></div><div class="layui-form-item"><label class="layui-form-label">url</label><div class="layui-input-block"><input type="text" name="url" required="" lay-verify="required" placeholder="url" autocomplete="off" class="layui-input" value="<?php echo url("","",true,false);?>"></div></div><div class="layui-form-item"><label class="layui-form-label">pagepath</label><div class="layui-input-block"><input type="text" name="pagepath" required="" lay-verify="required" placeholder="pagepath" autocomplete="off" class="layui-input" value="{pagepath}"></div></div></div>';
                                var _appid = '', _pagepath = '', _url = '';
                                if (content.indexOf(',') > 0) {
                                    _appid = content.split(',')[0] || '';
                                    _url = content.split(',')[1] || '';
                                    _pagepath = content.split(',')[2] || '';
                                }
                                $span.data('appid', _appid), $span.data('url', _url), $span.data('pagepath', _pagepath);
                                return tpl.replace('{appid}', _appid).replace('<?php echo url("","",true,false);?>', _url).replace('{pagepath}', _pagepath);
                            case 'customservice':
                            case 'text':
                                return '<div>回复内容<textarea style="resize:none;height:225px" name="content" class="form-control input-sm">{content}</textarea></div>'.replace('{content}', content);
                            case 'view':
                                return '<div class="layui-form-item layui-form-text"><label class="layui-form-label">跳转地址</label><div class="layui-input-block"><textarea placeholder="请输入内容" class="layui-textarea" name="content">{content}</textarea></div></div>'.replace('{content}', content);
                            case 'keys':
                                return '<div class="layui-form-item layui-form-text"><label class="layui-form-label">匹配内容</label><div class="layui-input-block"><textarea placeholder="请输入内容" class="layui-textarea" name="content">{content}</textarea></div></div>'.replace('{content}', content);
                            case 'event':
                                var options = {
                                    'scancode_push': '扫码推事件',
                                    'scancode_waitmsg': '扫码推事件且弹出“消息接收中”提示框',
                                    'pic_sysphoto': '弹出系统拍照发图',
                                    'pic_photo_or_album': '弹出拍照或者相册发图',
                                    'pic_weixin': '弹出微信相册发图器',
                                    'location_select': '弹出地理位置选择器'};
                                var select = [], tpl = '<div class="layui-form-item layui-form-text" style="margin-bottom: auto;"><label class="layui-form-label"></label><div><label><input class="cuci-radio" name="content" type="radio" {checked} value="{value}"> {title}</label></div></div>';
                                if (!(options[content] || false)) {
                                    content = 'scancode_push';
                                    $span.data('content', content);
                                }
                                for (var i in options) {
                                    select.push(tpl.replace('{value}', i).replace('{title}', options[i]).replace('{checked}', (i === content) ? 'checked' : ''));
                                }
                                return select.join('');
                        }
                    }.call(this);
                    var $html = $(html), $input = $html.find('input,textarea');
                    $input.on('change keyup click', function () {
                        // 将input值写入到span上
                        $span.data(this.name, $(this).val() || $(this).html());
                        // 如果是小程序，合并内容到span的content上
                        if (type === 'miniprogram') {
                            $span.data('content', $span.data('appid') + ',' + $span.data('url') + ',' + $span.data('pagepath'));
                        }
                    });
                    $('.editor-content-input').html($html);
                }).filter('input[value="{type}"]'.replace('{type}', type)).trigger('click');
            }
        };
        /**
         * 提交数据
         * @returns {undefined}
         */
        menu.prototype.submit = function () {
            var data = [];
            function getdata($span) {
                var menudata = {};
                menudata.name = $span.text();
                menudata.type = $span.attr('data-type');
                menudata.content = $span.data('content') || '';
                return menudata;
            }

            $('li.parent-menu').map(function (index, item) {
                if (!$(item).hasClass('menu-add')) {
                    var menudata = getdata($(item).find('a:first span'));
                    menudata.index = index + 1;
                    menudata.pindex = 0;
                    menudata.sub = [];
                    menudata.sort = index;
                    data.push(menudata);
                    $(item).find('.sub-menu ul li:not(.menu-add) span').map(function (ii, span) {
                        var submenudata = getdata($(span));
                        submenudata.index = (index + 1) + '' + (ii + 1);
                        submenudata.pindex = menudata.index;
                        submenudata.sort = ii;
                        data.push(submenudata);
                    });
                }
            });
//            console.log(data);return;
            var data = (data == '')?'':data;
            var load = layer.load(1);
            $.post(
                '<?php echo url("mp/Mp/menuedit"); ?>',
                {data:data},
                function (res) {
                    if(res.status=='0'){
                        layer.close(load);
                        layer.alert(res.msg);
                    }
                    if(res.status=='1'){
                        layer.close(load);
                        layer.msg(res.msg,{time:1000},function () {

                        });
                    }
                },
                "json"
            )
        };
        new menu();
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