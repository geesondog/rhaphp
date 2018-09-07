
![](https://raw.githubusercontent.com/geesondog/rhaphp/master/public/static/images/rhaphp_pc.png)

官方网站：https://www.rhaphp.com/

交流社区：https://ask.rhaphp.com/

高级群：672313914(快速回应)

免费群：204201861(允许扯谈) 

进入公众号看演示

![](https://raw.githubusercontent.com/geesondog/rhaphp/master/public/static/images/rhaphp_qrcode.jpg)

 RhaPHP微信免费开源公众号管理系统，支持多公众号管理，平台独立且快速简洁易用。灵活的扩展应用机制，具有容易上手，几乎融合微信接口，简单的调用对二次开发与开发扩展应用模块大大提高开发效率，降低企业商家运营成本。扩展应用模块化，机制灵活，代码简单并快速上手。基于THINKPHP5强力内核驱动与LAYUI前端框架，支持 Linux/Windows/Mac。我们致力长期更新，永久免费开源！ 
 
 
## 安装系统
把系统所需要文件上传到你的网站根目录，addons,data,uploads,runtime,config权限为777。访问http:域名/index.php/install/即可进入安装。PHP5.6或以上，但是为了最佳运行效果我们建议你使用PHP7.0或以上的版本，MYSQL建议在5.5或以上注意字符是utf8mb4。如果你在使用过程中遇到问题，请访问官方网站或者联系我们。也可邮件方式：qimengkeji@vip.qq.com


## 系统目录结构
~~~
www 
├─addons                应用插件目录
│  ├─myApp              应用名称
│     ├─controller      此控制器目录
│     ├─model           此模型目录
│     ├─view            视图目录
│     ├─static          此应用的静态目录JS,CSS,IMAGE
│     ├─common.php      此应用的函数文件
│     ├─config.php      此应用的配置文件
│     ├─install.sql     此应用的数据库安装文件
│     └─logo.jpg        此应用的LOGO
├─application           应用目录
│  ├─admin              后台模块
│  ├─behavior           行为目录
│  ├─common             公共模块
│  ├─install            系统安装模块
│  ├─mp                 操作管理微信公众号模块
│  ├─command.php        命令行工具配置文件
│  ├─common.php         公共函数文件
│  └─tags.php           应用行为扩展定义文件
│
├─public                静态目录JS,CSS,IMAGE
├─data                  存放数据
├─config                应用配置目录
│  ├─module_name        模块配置目录
│  │  ├─database.php    数据库配置
│  │  ├─cache           缓存配置
│  │  └─ ...            
│  │
│  ├─app.php            应用配置
│  ├─cache.php          缓存配置
│  ├─cookie.php         Cookie配置
│  ├─database.php       数据库配置
│  ├─log.php            日志配置
│  ├─session.php        Session配置
│  ├─template.php       模板引擎配置
│  └─trace.php          Trace配置
├─route                 路由定义目录
│  ├─route.php          路由定义
│  └─...                更多
├─runtime               应用的运行时目录
├─thinkphp              框架系统目录
│  ├─lang               语言文件目录
│  ├─library            框架类库目录
│  │  ├─think           Think类库包目录
│  │  └─traits          系统Trait目录
│  │
│  ├─tpl                系统模板目录
│  ├─base.php           基础定义文件
│  ├─convention.php     框架惯例配置文件
│  ├─helper.php         助手函数文件
│  └─logo.png           框架LOGO文件
│
├─extend                扩展类库目录
├─vendor                第三方类库目录（Composer依赖库）
├─build.php             自动生成定义文件（参考）
├─安装协议.txt
├─README.md             README 文件
├─index.php             系统入口文件
├─composer.json         composer 定义文件
├─think                 命令行入口文件
~~~
