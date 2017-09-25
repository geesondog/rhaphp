<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:69:"/Users/ima/GitHub/rhaphp/application/install/view/index/complete.html";i:1505629268;s:66:"/Users/ima/GitHub/rhaphp/application/install/view/public/base.html";i:1505625599;}*/ ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>RhaPHP系统安装</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">
<link href="__STATIC__/bootstrap/css/bootstrap.css" rel="stylesheet">
<script src="__STATIC__/jquery/jquery-1.9.1.min.js"></script>
<style>
body{font-family: "Microsoft Yahei",'新宋体';}
.container{background: #ffffff; margin: 50px auto; padding: 20px 0; width: 1024px;}
.header-title{border-bottom: 1px solid #dedede; margin-bottom: 10px;}
.progress-tool{padding: 10px;}
.progress{height: 30px;}
.progress-bar{line-height: 30px; font-size: 14px;}
.article{padding: 0 20px;}
h1{font-size: 18px; color: #333333; font-weight: bold;}
h2{font-size: 16px; color: #333333; font-weight: bold;}
</style>
</head>

<body style="background:  rgb(230, 234, 234)">
<div class="container">
	<div class="margin">
		<div class="text-center header-title margin-top">
			<h1>RhaPHP系统<?php if(session('update')): ?>升级<?php else: ?>安装<?php endif; ?></h1>
		</div>
		<div class="progress-tool">
			<div class="progress">
				<div class="progress-bar progress-bar-<?php echo $status['index']; ?> progress-bar-striped" style="width: 20%">
					<span>系统安装</span>
				</div>
				<div class="progress-bar progress-bar-<?php echo $status['check']; ?> progress-bar-striped" style="width: 20%">
					<span>环境检查</span>
				</div>
				<div class="progress-bar progress-bar-<?php echo $status['config']; ?> progress-bar-striped" style="width: 20%">
					<span>系统配置</span>
				</div>
				<div class="progress-bar progress-bar-<?php echo $status['sql']; ?> progress-bar-striped" style="width: 20%">
					<span>数据库安装</span>
				</div>
				<div class="progress-bar progress-bar-<?php echo $status['complete']; ?> progress-bar-striped" style="width: 20%">
					<span>安装完成</span>
				</div>
			</div>
		</div>
		<div class="article margin-top">
			
<h1 class="text-center">恭喜：安装完成</h1>
<div class="row">
	<div class="col-sm-12">
		<ul class="list-group">
			<li class="list-group-item"><a href="http://www.rhaphp.com" target="_blank">RhaPHP官方网站</a></li>
			<li class="list-group-item"><a href="http://www.rhaphp.com" target="_blank">RhaPHP开发文档</a></li>
		</ul>
	</div>
</div>


			<div class="margin-top">
				
<div class="text-center">
    <a class="btn btn-primary" target="_blank" href="<?php echo url('/admin'); ?>">登录后台</a>
    <a class="btn btn-success" target="_blank" href="<?php echo url('/'); ?>">访问首页</a>
</div>

			</div>
		</div>
	</div>
</div>
</body>
</html>