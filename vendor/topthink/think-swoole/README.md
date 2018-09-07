ThinkPHP 5.1 Swoole 扩展
===============

## 安装

首先按照Swoole官网说明安装swoole扩展，然后使用
~~~
composer require topthink/think-swoole
~~~
安装swoole扩展。

## 使用方法

### HttpServer

直接在命令行下启动服务端。

~~~
php think swoole
~~~

启动完成后，会在0.0.0.0:9501启动一个HTTP Server，可以直接访问当前的应用。

swoole的参数可以在应用配置目录下的swoole.php里面配置（具体参考配置文件内容）。

如果需要使用守护进程方式运行，可以使用
~~~
php think swoole -d
~~~
或者在swoole.php文件中设置
~~~
'daemonize'	=>	true
~~~

注意：由于onWorkerStart运行的时候没有HTTP_HOST，因此最好在应用配置文件中设置app_host

支持的操作包括
~~~
php think swoole [start|stop|reload|restart]
~~~

### Server

可以支持直接启动一个Swoole server

~~~
php think swoole:server
~~~
会在0.0.0.0:9508启动一个Websocket服务。

如果需要自定义参数，可以在config/swoole_server.php中进行配置，包括：

配置参数 | 描述
--- | ---
type| 服务类型
host | 监听地址
port | 监听端口
mode | 运行模式
sock_type | Socket type


并且支持swoole所有的参数。
也支持使用闭包方式定义相关事件回调。

~~~
return [
    // 扩展自身配置
    'host'         => '0.0.0.0', // 监听地址
    'port'         => 9501, // 监听端口
    'type'         => 'socket', // 服务类型 支持 socket http server
    'mode'         => SWOOLE_PROCESS,
    'sock_type'    => SWOOLE_SOCK_TCP,

    // 可以支持swoole的所有配置参数
    'daemonize'    => false,

    // 事件回调定义
    'onOpen'       => function ($server, $request) {
        echo "server: handshake success with fd{$request->fd}\n";
    },

    'onMessage'    => function ($server, $frame) {
        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
        $server->push($frame->fd, "this is server");
    },

    'onRequest'    => function ($request, $response) {
        $response->end("<h1>Hello Swoole. #" . rand(1000, 9999) . "</h1>");
    },

    'onClose'      => function ($ser, $fd) {
        echo "client {$fd} closed\n";
    },
];
~~~

也可以使用自定义的服务类

~~~
<?php
namespace app\http;

use think\swoole\Server;

class Swoole extends Server
{
	protected $host = '127.0.0.1';
	protected $port = 9502;
    protected $serverType = 'socket';
	protected $option = [ 
		'worker_num'=> 4,
		'daemonize'	=> true,
		'backlog'	=> 128
	];

	public function onReceive($server, $fd, $from_id, $data)
	{
		$server->send($fd, 'Swoole: '.$data);
	}
}
~~~

支持swoole所有的回调方法定义（回调方法必须是public类型）
serverType 属性定义为 socket或者http 则支持swoole的swoole_websocket_server和swoole_http_server

然后在swoole_server.php中增加配置参数：
~~~
return [
	'swoole_class'	=>	'app\http\Swoole',
];
~~~

定义该参数后，其它配置参数均不再有效。

在命令行启动服务端
~~~
php think swoole:server
~~~


支持reload|restart|stop|status 操作
~~~
php think swoole:server reload
~~~


