<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

\think\Console::addDefaultCommands([
    '\\think\\worker\\command\\Worker',
    '\\think\\worker\\command\\Server',
]);

\think\Facade::bind([
    \think\worker\facade\Application::class => \think\worker\Application::class,
    \think\worker\facade\Worker::class      => \think\worker\Worker::class,
]);
