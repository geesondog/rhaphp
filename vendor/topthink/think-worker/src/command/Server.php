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

namespace think\worker\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\Config;
use think\worker\Server as WorkerServer;
use Workerman\Worker;

/**
 * Worker Server 命令行类
 */
class Server extends Command
{
    protected $config = [];

    public function configure()
    {
        $this->setName('worker:server')
            ->addArgument('action', Argument::OPTIONAL, "start|stop|restart|reload|status", 'start')
            ->addOption('daemon', 'd', Option::VALUE_NONE, 'Run the workerman server in daemon mode.')
            ->setDescription('Workerman Server for ThinkPHP');
    }

    public function execute(Input $input, Output $output)
    {
        $action = $input->getArgument('action');

        $this->config = Config::pull('worker_server');

        if (DIRECTORY_SEPARATOR !== '\\') {
            if (!in_array($action, ['start', 'stop', 'reload', 'restart', 'status'])) {
                $output->writeln("<error>Invalid argument action:{$action}, Expected start|stop|restart|reload|status .</error>");
                return false;
            }

            global $argv;
            array_shift($argv);
            array_shift($argv);
            array_unshift($argv, 'think', $action);
        } elseif ('start' != $action) {
            $output->writeln("<error>Not Support action:{$action} on Windows.</error>");
            return false;
        }

        // 自定义服务器入口类
        if (!empty($this->config['worker_class'])) {
            $class = $this->config['worker_class'];

            if (class_exists($class)) {
                $worker = new $class;
                if (!$worker instanceof WorkerServer) {
                    $output->writeln("<error>Worker Server Class Must extends \\think\\worker\\Server</error>");
                }
            } else {
                $output->writeln("<error>Worker Server Class Not Exists : {$class}</error>");
            }
            return;
        }

        $output->writeln('Starting Workerman server...');

        if (!empty($this->config['socket'])) {
            $socket = $this->config['socket'];
        } else {
            $host     = !empty($this->config['host']) ? $this->config['host'] : '0.0.0.0';
            $port     = !empty($this->config['port']) ? $this->config['port'] : 2345;
            $protocol = !empty($this->config['protocol']) ? $this->config['protocol'] : 'websocket';
            $socket   = $protocol . '://' . $host . ':' . $port;
            unset($this->config['host'], $this->config['port'], $this->config['protocol']);
        }

        if (isset($this->config['context'])) {
            $context = $this->config['context'];
            unset($this->config['context']);
        } else {
            $context = [];
        }

        $worker = new Worker($socket, $context);

        // 开启守护进程模式
        if ($this->input->hasOption('daemon')) {
            Worker::$daemonize = true;
        }

        if (!empty($this->config['ssl'])) {
            $this->config['transport'] = 'ssl';
            unset($this->config['ssl']);
        }

        // 设置服务器参数
        foreach ($this->config as $name => $val) {
            if (in_array($name, ['stdoutFile', 'daemonize', 'pidFile', 'logFile'])) {
                Worker::${$name} = $val;
            } else {
                $worker->$name = $val;
            }
        }

        // Run worker
        Worker::runAll();
    }
}
