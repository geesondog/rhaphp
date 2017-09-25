<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:51:"/Users/ima/GitHub/rhaphp/themes/pc/mp/mp/index.html";i:1506326852;s:63:"/Users/ima/GitHub/rhaphp/themes/pc/mp/../admin/common/base.html";i:1505801784;}*/ ?>
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
        
<style>
    .ui_trendgrid {
        table-layout: fixed;
        position: relative;
        width: 100%;
        margin: 20px 0px;
    }

    .ui_trendgrid td {

        border-right: 1px solid rgb(231, 231, 235);
    }

    .ui_trendgrid td.last {
        border-right: 0px none;
    }

    .ui_trendgrid dl {
        display: inline-block;
        margin-top: 0px;
        padding: 0px;
        text-align: left;
        position: relative;
        z-index: 2;
    }

    .ui_trendgrid dt {
        padding-bottom: 12px;
        font-size: 14px;
        font-weight: normal;
        text-align: center;
    }

    .ui_trendgrid dd {
        margin-top: 2px;
        font-size: 14px;
        line-height: 18px;
        white-space: nowrap;
    }

    .ui_trendgrid dd.ui_trendgrid_number {
        text-align: center;
        color: rgb(103, 103, 103);
        font-size: 30px;
        margin-right: 10px;
        margin-bottom: 15px;
    }

    .ui_trendgrid dd.ui_trendgrid_number strong {
        font-weight: 400;
        font-style: normal;
    }

    .ui_trendgrid .icon_down, .ui_trendgrid .icon_up, .ui_trendgrid .icon_down_grey {
        margin-left: 10px;
        position: relative;
        top: -2px;
        margin-right: 3px;
        display: inline-block;
        width: 10px;
        height: 9px;
        vertical-align: middle;
    }

    .ui_trendgrid .icon_down {
        background-position: -10px 0px;
    }

    .ui_trendgrid_item {
        height: 100%;
        position: relative;
        overflow: hidden;
        text-align: center;
    }

    .ui_trendgrid_item b {
        font-weight: 400;
        font-style: normal;
        font-size: 14px;
    }

    .ui_trendgrid_chart {
        width: 100%;
        position: absolute;
        bottom: 0px;
        left: 1px;
    }

    .ui_trendgrid_unit {
        margin-bottom: 20px;
        font-size: 18px;
        font-weight: 400;
        font-style: normal;
    }

    .step_inner::after {
        content: "​";
        display: block;
        height: 0px;
        clear: both;
    }

    .tab_navs::after {
        content: "​";
        display: block;
        height: 0px;
        clear: both;
    }

    .info_box {
        margin-bottom: 20px;
    }

    .info_box .inner {
        border: 1px solid rgb(231, 231, 235);
    }

    .info_box .info_hd {
        line-height: 38px;
        height: 38px;
        padding: 0px 20px;
        background-color: rgb(244, 245, 249);
        border-bottom: 1px solid rgb(231, 231, 235);
    }

    .info_box .info_hd::after {
        content: "​";
        display: block;
        height: 0px;
        clear: both;
    }

    .info_box .info_hd .ext_info {
        float: right;
    }

    .info_box .info_hd h4 {
        font-weight: 400;
        font-size: 14px;
    }

    .inner {
        position: relative;
    }

    .page_msg.top {
        margin-top: 6px;
        margin-bottom: 20px;
    }

    .wrp_overview {
        padding: 0px 30px 40px;
        position: relative;
        margin-top: 20px;
    }

    .info_hd.append_ask {
        position: relative;
        z-index: 10;
    }

    .info_hd.append_ask .help {
        right: 10px;
        top: 0px;
    }

    .info_bd {
        position: relative;
        z-index: 9;
    }

    .page_user .help .help_content {
        top: -9px;
    }

    .page_user_stat.mini {
        padding: 20px 15px 0px;
    }

    .page_user_stat.mini .inner {
        padding-left: 15px;
        background-color: transparent;
    }

    .page_user_stat.mini .inner.stat_info {
        background-color: rgb(224, 234, 246);
    }

    .table .table_action.arrow::after {
        content: "";
        position: relative;
        top: 13px;
        border-width: 5px;
        border-style: solid;
        border-color: rgb(198, 198, 198) transparent transparent;
        -moz-border-top-colors: none;
        -moz-border-right-colors: none;
        -moz-border-bottom-colors: none;
        -moz-border-left-colors: none;
        border-image: none;
    }

    .tr_chosen .table_action.arrow::after {
        top: -11px;
        border-width: 5px;
        border-style: solid;
        border-color: transparent transparent rgb(198, 198, 198);
        -moz-border-top-colors: none;
        -moz-border-right-colors: none;
        -moz-border-bottom-colors: none;
        -moz-border-left-colors: none;
        border-image: none;
    }
</style>

<div class="info_box" style="padding: 10px;">
    <div class="inner">
        <div class="info_hd append_ask"><h4>今日关键指标</h4>
        </div>
        <div class="info_bd">
            <div class="content" id="js_keydata">
                <table class="ui_trendgrid ui_trendgrid_3">
                    <tbody>
                    <tr>
                        <td class="first">
                            <div class="ui_trendgrid_item">
                                <div class="ui_trendgrid_chart"></div>
                                <dl>
                                    <dt><b>新关注人数</b></dt>
                                    <dd class="ui_trendgrid_number"><strong><?php echo $report['subscribe']['today']; ?></strong><em
                                            class="ui_trendgrid_unit"></em></dd>
                                    <dd>昨天 <i class="icon_down"></i><?php echo $report['subscribe']['yesterday']; ?></dd>
                                    <dd>本周 <i class="icon_up" ></i><?php echo $report['subscribe']['week']; ?></dd>
                                    <dd>上周 <i class="icon_up"></i><?php echo $report['subscribe']['lastweek']; ?></dd>
                                    <dd>本月 <i class="icon_up"></i><?php echo $report['subscribe']['month']; ?></dd>
                                    <dd>上月 <i class="icon_up"></i><?php echo $report['subscribe']['lastmonth']; ?></dd>
                                    <dd>本年 <i class="icon_up"></i><?php echo $report['subscribe']['year']; ?></dd>
                                    <dd>去年 <i class="icon_up"></i><?php echo $report['subscribe']['lastyear']; ?></dd>
                                </dl>
                            </div>
                        </td>
                        <td>
                            <div class="ui_trendgrid_item">
                                <div class="ui_trendgrid_chart"></div>
                                <dl>
                                    <dt><b>取消关注人数</b></dt>
                                    <dd class="ui_trendgrid_number"><strong><?php echo $report['subscribe']['today']; ?></strong><em
                                            class="ui_trendgrid_unit"></em></dd>
                                    <dd>昨天 <i class="icon_down"></i><?php echo $report['unsubscribe']['yesterday']; ?></dd>
                                    <dd>本周 <i class="icon_up" ></i><?php echo $report['unsubscribe']['week']; ?></dd>
                                    <dd>上周 <i class="icon_up"></i><?php echo $report['unsubscribe']['lastweek']; ?></dd>
                                    <dd>本月 <i class="icon_up"></i><?php echo $report['unsubscribe']['month']; ?></dd>
                                    <dd>上月 <i class="icon_up"></i><?php echo $report['unsubscribe']['lastmonth']; ?></dd>
                                    <dd>本年 <i class="icon_up"></i><?php echo $report['unsubscribe']['year']; ?></dd>
                                    <dd>去年 <i class="icon_up"></i><?php echo $report['unsubscribe']['lastyear']; ?></dd>
                                </dl>
                            </div>
                        </td>
                        <td>
                            <div class="ui_trendgrid_item">
                                <div class="ui_trendgrid_chart"></div>
                                <dl>
                                    <dt><b>净增关注人数</b></dt>
                                    <dd class="ui_trendgrid_number"><strong><?php echo $report['subscribe']['today']-$report['subscribe']['today']; ?></strong><em
                                            class="ui_trendgrid_unit"></em></dd>
                                    <dd>昨天 <i class="icon_down"></i><?php echo $report['subscribe']['yesterday']-$report['unsubscribe']['yesterday']; ?></dd>
                                    <dd>本周 <i class="icon_up" ></i><?php echo $report['subscribe']['week']-$report['unsubscribe']['week']; ?></dd>
                                    <dd>上周 <i class="icon_up"></i><?php echo $report['subscribe']['lastweek']-$report['unsubscribe']['lastweek']; ?></dd>
                                    <dd>本月 <i class="icon_up"></i><?php echo $report['subscribe']['month']-$report['unsubscribe']['month']; ?></dd>
                                    <dd>上月 <i class="icon_up"></i><?php echo $report['subscribe']['lastmonth']-$report['unsubscribe']['lastmonth']; ?></dd>
                                    <dd>本年 <i class="icon_up"></i><?php echo $report['subscribe']['year']-$report['unsubscribe']['year']; ?></dd>
                                    <dd>去年 <i class="icon_up"></i><?php echo $report['subscribe']['lastyear']-$report['unsubscribe']['lastyear']; ?></dd>
                                </dl>
                            </div>
                        </td>
                        <td class="last">
                            <div class="ui_trendgrid_item">
                                <div class="ui_trendgrid_chart"></div>
                                <dl>
                                    <dt><b>当前关注人数</b></dt>
                                    <dd class="ui_trendgrid_number"><strong><?php echo $report['total']['subscribe_total']; ?></strong><em
                                            class="ui_trendgrid_unit"></em></dd>
                                    <dd>累积关注 <i class="icon_up"></i><?php echo $report['total']['total']; ?></dd>
                                    <dd>累积取关 <i class="icon_down"></i><?php echo $report['total']['unsubscribe_total']; ?></dd>
                                </dl>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <p class="tip_for_p" style="margin-top: 7px;">
        注：以上数据以接入平台后产生的数据为准，若公众号已认证请先同步粉丝。
    </p>
        <div class="layui-row" style="padding:10px 0px 10px 0px;">
            <div class="layui-col-md6" style="padding-right: 5px;">
                <div class="layui-collapse">
                    <div class="layui-colla-item">
                        <h2 class="layui-colla-title">风向标</h2>
                        <div class="layui-colla-content layui-show">
                            <ul>
                                <?php if(is_array($data_by_api) || $data_by_api instanceof \think\Collection || $data_by_api instanceof \think\Paginator): $i = 0; $__LIST__ = $data_by_api;if( count($__LIST__)==0 ) : echo "暂时没有数据" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                <li><a href="<?php echo $vo['link']; ?>" target="_blank"><?php echo $vo['title']; ?></a> </li>
                                <?php endforeach; endif; else: echo "暂时没有数据" ;endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-col-md6" style="padding-left: 5px;">
                <div class="layui-collapse">
                    <div class="layui-colla-item">
                        <h2 class="layui-colla-title">热门应用</h2>
                        <div class="layui-colla-content layui-show">
                            <ul class="rha_app_list">
                            <?php if(is_array($app_by_api) || $app_by_api instanceof \think\Collection || $app_by_api instanceof \think\Paginator): $i = 0; $__LIST__ = $app_by_api;if( count($__LIST__)==0 ) : echo "暂时没有数据" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                <li><a href="<?php echo $vo['link']; ?>" target="_blank" title="<?php echo $vo['name']; ?>"><img src="<?php echo $vo['logo']; ?>" ><span><?php echo $vo['name']; ?></span></a> </li>
                            <?php endforeach; endif; else: echo "暂时没有数据" ;endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>


</div>


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