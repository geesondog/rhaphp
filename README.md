##官方网站：http://www.rhaphp.com/

微信公众号管理系统，也是一套微信公众号开发框架。支持移动管理，几乎集合微信功能，简洁、快速上手、快速开发微信各种各样应用。 
安装系统
把系统所需要文件上传到你的网站根目录，data,uploads权限为777。访问http:域名/index.php/install/即可进入安装。PHP5.5或以上，但是为了最佳运行效果我们建议你使用PHP7.0或以上的版本，MYSQL建议在5.5或以上注意字符是utf8mb4。如果你在使用过程中遇到问题，请访问官方网站或者联系我们。也可邮件方式：qimengkeji@vip.qq.com
## 系统目录结构
~~~
www 
├─addons                应用插件目录
│  ├─myApp              应用名称
│     ├─controller      此控制器目录
│     ├─model           此模型目录
│     ├─view            视图目录
│     ├─common.php      此应用的函数文件
│     ├─config.php      此应用的配置文件
│     └─logo.jpg        此应用的LOGO
├─application           应用目录
│  ├─admin              后台模块
│  ├─behavior           行为目录
│  ├─common             公共模块
│  ├─install            系统安装模块
│  ├─mp                 操作管理微信公众号模块
│  ├─command.php        命令行工具配置文件
│  ├─common.php         公共函数文件
│  ├─config.php         公共配置文件
│  ├─route.php          路由配置文件
│  ├─tags.php           应用行为扩展定义文件
│  └─database.php       数据库配置文件
│
├─public                静态目录JS,CSS,IMAGE
├─data                  存放数据与runtime
│
├─thinkphp              框架系统目录
│  ├─lang               语言文件目录
│  ├─library            框架类库目录
│  │  ├─think           Think类库包目录
│  │  └─traits          系统Trait目录
│  │
│  ├─tpl                系统模板目录
│  ├─base.php           基础定义文件
│  ├─console.php        控制台入口文件
│  ├─convention.php     框架惯例配置文件
│  ├─helper.php         助手函数文件
│  ├─phpunit.xml        phpunit配置文件
│  └─start.php          框架入口文件
│
├─extend                扩展类库目录
├─vendor                第三方类库目录（Composer依赖库）
├─安装协议.txt
├─README.md             README 文件
├─index.php             系统入口文件
~~~
