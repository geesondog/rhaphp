<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:66:"/Users/ima/GitHub/rhaphp/application/install/view/index/index.html";i:1505625599;s:66:"/Users/ima/GitHub/rhaphp/application/install/view/public/base.html";i:1505625599;}*/ ?>
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
			
<div class="margin-top">

	<header>
		<h2 class="text-center">__NAME__ 安装协议</h2>

		<section class="text-center abstract">版权所有 (c) 2017，__COMPANY__保留所有权利。</section>
	</header>
	<section class="article-content" style="text-indent: 2em">
		<p>
			__NAME__(简称有：RhaPHP、二哈系统)基于
			<a target="_blank" href="http://www.thinkphp.cn">ThinkPHP</a>框架
			的开发产品。感谢顶想公司为__NAME__提供内核支持。
		</p>

		<p>
			感谢您选择__NAME__，希望我们的努力可以为您创造价值。
			产品官方网站网址为
			<a href="http://__WEBSITE__" target="_blank">http://__WEBSITE__</a>
			。
		</p>

		<p>
			用户须知：本协议是您于__COMPANY__关于__NAME__产品使用的法律协议。无论您是个人或组织、盈利与否、用途如何（包括以学习和研究为目的），均需仔细阅读本协议，包括免除或者限制__COMPANY__责任的免责条款及对您的权利限制。请您审阅并接受或不接受本服务条款。如您不同意本服务条款及或__COMPANY__随时对其的修改，您应不使用或主动取消__NAME__产品。否则，您的任何对__NAME__的相关服务的注册、登陆、下载、查看等使用行为将被视为您对本服务条款全部的完全接受，包括接受__COMPANY__对服务条款随时所做的任何修改。
		</p>

		<p>
			本服务条款一旦发生变更,__COMPANY__将在产品官网上公布修改内容。修改后的服务条款一旦在网站公布即有效代替原来的服务条款。您可随时登陆官网查阅最新版服务条款。如果您选择接受本条款，即表示您同意接受协议各项条件的约束。如果您不同意本服务条款，则不能获得使用本服务的权利。您若有违反本条款规定，__COMPANY__有权随时中止或终止您对__NAME__产品的使用资格并保留追究相关法律责任的权利。
		</p>

		<p>
			在理解、同意、并遵守本协议的全部条款后，方可开始使用__NAME__产品。您也可以与__COMPANY__直接签订另一书面协议，以补充或者取代本协议的全部或者任何部分。
		</p>

		<p>
			__COMPANY__拥有__NAME__的知识产权，包括著作权。本软件只供许可协议，并非出售。__COMPANY__只允许您在遵守本协议各项条款的情况下复制、下载、安装、使用或者以其他方式受益于本软件的功能或者知识产权。
		</p>
		<p>__NAME__遵循Apache Licence2开源协议，并且免费使用（但不包括其衍生产品、插件与应用或者服务）。<br/>
		<p>
			<strong>协议规定的约束和限制 </strong>
		<ol style="text-indent:0;">
			<li>未经官方许可，不得将本软件版权与包括RhaPHP字样修改、删除或者隐藏。</li>
			<li>未经官方许可，不得将本软件用于商业用途（包括但不限于企业网站、经营性网站、以营利为目的或实现盈利的网站）。</li>
			<li>未经官方许可，不得对本软件进行出租、出售、抵押或发放子许可证。</li>
			<li>未经官方许可，禁止在RhaPHP的整体或任何部分基础上以发展任何派生版本、修改版本或第三方版本用于重新分发。</li>
			<li>如果您未能遵守本协议的条款，并承担相应法律责任。</li>
		</ol>
		</p>
		<p>
			<strong>有限担保和免责声明 </strong>
		<ol style="text-indent:0;">
			<li>本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的。</li>
			<li>用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未购买产品技术服务之前，我们不承诺对免费用户提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任。</li>
			<li>电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和等同的法律效力。您一旦开始确认本协议并安装  RhaPHP，即被视为完全理解并接受本协议的各项条款，在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。</li>
			<li>如果本软件带有其它软件的整合API示范例子包，这些文件版权不属于本软件官方，并且这些文件是没经过授权发布的，请参考相关软件的使用许可合法的使用。</li>
		</ol>
		</p>
	</section>

</div>


			<div class="margin-top">
				
<div class="text-center">
	<a class="btn btn-primary" href="<?php echo url('Install/index/check'); ?>">同意安装协议</a>
	<a class="btn btn-default" style="background: white;" href="http://__WEBSITE__">不同意</a>
</div>

			</div>
		</div>
	</div>
</div>
</body>
</html>