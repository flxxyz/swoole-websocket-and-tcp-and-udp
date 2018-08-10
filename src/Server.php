<?php

namespace swoole_websocket_and_tcp_and_udp;


use swoole_websocket_and_tcp_and_udp\common\Logger;
use swoole_websocket_and_tcp_and_udp\common\ProcessTrait;
use swoole_websocket_and_tcp_and_udp\common\Swoole;
use swoole_websocket_and_tcp_and_udp\handler\HttpHandler;
use swoole_websocket_and_tcp_and_udp\handler\ServerHandler;
use swoole_websocket_and_tcp_and_udp\handler\WebsocketHandler;

/**
 * @description 此文件多处借鉴 @laravel-s
 * @link        https://github.com/hhxsv5/laravel-s/blob/master/src/Swoole/Server.php#L65
 */
class Server
{
    use ProcessTrait, Logger, Swoole;

    protected $port;

    protected $config;

    /**
     * @var \swoole_websocket_server | \swoole_http_server
     */
    protected $server;

    protected $primaryConfig = [];

    protected $enableWebsocket = false;

    protected $enableTask = false;

    protected $otherConfig = [];

    public function __construct($config)
    {
        $this->checkSwoole('1.10.4');

        $this->primaryConfig = [
            'host'    => '0.0.0.0',
            'port'    => '8999',
            'type'    => SWOOLE_SOCK_TCP,
            'setting' => [
                'daemonize'          => false,
                'open_http_protocol' => true,
            ],
            'handler' => \swoole_websocket_and_tcp_and_udp_test\http::class,
        ];
        $serverClass = \swoole_http_server::class;

        $this->config = $config;
        ini_set('date.timezone', $this->config['timezone']);

        if (isset($this->config['websocket'])) {
            if ($this->config['websocket']['enable']) {
                $this->enableWebsocket = true;
                $this->primaryConfig = $this->config['websocket'];
                $this->primaryConfig['setting']['open_websocket_protocol']
                    = true;
                if (!isset($this->primaryConfig['setting']['open_http_protocol'])) {
                    $this->primaryConfig['setting']['open_http_protocol']
                        = true;
                }
                if (!isset($this->primaryConfig['setting']['daemonize'])) {
                    $this->primaryConfig['setting']['daemonize'] = false;
                }
                $serverClass = \swoole_websocket_server::class;
                unset($this->config['websocket']);

                goto start;
            }
        }

        if (isset($this->config['http'])) {
            if ($this->config['http']['enable']) {
                $this->primaryConfig = $this->config['http'];
                $this->primaryConfig['setting']['open_http_protocol'] = true;
                if (!isset($this->primaryConfig['setting']['daemonize'])) {
                    $this->primaryConfig['setting']['daemonize'] = false;
                }
                $serverClass = \swoole_http_server::class;
                unset($this->config['http']);

                goto start;
            }
        }

        start:

        foreach ($this->config as $name => $item) {
            if (!is_array($item)) {
                continue;
            }

            $this->otherConfig[$name] = $item;
        }
//        var_dump($this->otherConfig);exit(1);

        $host = $this->primaryConfig['host'];
        $port = $this->primaryConfig['port'];
        $type = $this->primaryConfig['type'];
        $setting = $this->primaryConfig['setting'];

        if (isset($setting['task_worker_num'])) {
            $this->enableTask = true;
        }

        $serverName = $this->enableWebsocket?'websocket':'http';
        Logger::info("开始监听 [{$serverName}] {$host}:{$port}");
        $this->server = new $serverClass($host, $port, SWOOLE_PROCESS, $type);
        $this->server->set($setting);


        $this->bindBaseEvent();
        $this->bindTaskEvent();
        $this->bindMasterEvent();
        $this->bindOtherEvent();
    }

    protected function bindBaseEvent()
    {
        $this->server->on('Start', [$this, 'Start']);
        $this->server->on('Shutdown', [$this, 'Shutdown']);
        $this->server->on('ManagerStart', [$this, 'ManagerStart']);
        $this->server->on('ManagerStop', [$this, 'ManagerStop']);
        $this->server->on('WorkerStart', [$this, 'WorkerStart']);
        $this->server->on('WorkerStop', [$this, 'WorkerStop']);
        $this->server->on('WorkerExit', [$this, 'WorkerExit']);
        $this->server->on('WorkerError', [$this, 'WorkerError']);
        $this->server->on('PipeMessage', [$this, 'PipeMessage']);
    }

    protected function bindTaskEvent()
    {
        $this->server->on('Task', [$this, 'task']);
        $this->server->on('Finish', [$this, 'finish']);
    }

    protected function bindMasterEvent()
    {
        $handlerClass = $this->primaryConfig['handler'];

        if ($this->enableWebsocket) {
            $websocketHandler = new WebsocketHandler($handlerClass);
            $eventHandler = $websocketHandler->make();

            $this->server->on('Open', function () use ($eventHandler) {
                $eventHandler('Open', func_get_args());
            });

            $this->server->on('Message', function () use ($eventHandler) {
                $eventHandler('Message', func_get_args());
            });

            $this->server->on('Close', function () use ($eventHandler) {
                $eventHandler('Close', func_get_args());
            });
        } else {
            $httpHandler = new HttpHandler($handlerClass);
            $eventHandler = $httpHandler->make();

            $this->server->on('Request', function () use ($eventHandler) {
                $eventHandler('Request', func_get_args());
            });
        }
    }

    protected function bindOtherEvent()
    {
        foreach ($this->otherConfig as $serverName => $event) {
            $setting = isset($event['setting']) ? $event['setting'] : [];
            if (!$event['enable']) {
                continue;
            }

            if($serverName == 'http') {
                if (!isset($setting['daemonize'])) {
                    $setting['daemonize'] = false;
                }

                $setting['open_http_protocol'] = true;
            }

            $port = $this->server->listen($event['host'], $event['port'],
                $event['type']);
            Logger::info("开始监听 [{$serverName}] {$event['host']}:{$event['port']}");
            if (!($port instanceof \swoole_server_port)) {
                $errno = method_exists($this->server, 'getLastError')
                    ? $this->server->getLastError() : 'unknown';
                $errstr = sprintf('listen %s:%s failed: errno=%s',
                    $event['host'], $event['port'], $errno);
                Logger::err($errstr);
                continue;
            }

            $port->set($setting);

            if(!isset($event['handler']) || !class_exists($event['handler'])) {
                Logger::warn("请创建{$serverName}的执行代码！！！");
                continue;
            }

            $handlerClass = $event['handler'];

            $serverHandler = new ServerHandler($port, $handlerClass);
            $eventHandler = $serverHandler->make();

            static $events = [
                'Open',
                'Request',
                'Message',
                'Connect',
                'Close',
                'Receive',
                'Packet',
                'BufferFull',
                'BufferEmpty',
            ];

            foreach ($events as $event) {
                $port->on($event, function () use ($event, $eventHandler) {
                    $eventHandler($event, func_get_args());
                });
            }
        }
    }

    public function Start(\swoole_http_server $server)
    {
        foreach (spl_autoload_functions() as $function) {
            spl_autoload_unregister($function);
        }

        $this->setProcessName('master process');

        if (version_compare(\swoole_version(), '1.10.4', '>=')) {
            file_put_contents($this->config['pid_file'], $server->master_pid);
        }
    }

    public function Shutdown(\swoole_http_server $server)
    {

    }

    public function ManagerStart(\swoole_http_server $server)
    {

    }

    public function ManagerStop(\swoole_http_server $server)
    {

    }

    public function WorkerStart(\swoole_http_server $server, $worker_id)
    {
        if ($worker_id >= (swoole_cpu_num() * 2)) {
            $process = 'task worker';
        } else {
            $process = 'worker';
        }

        $this->setProcessName(sprintf('%s process %d', $process,
            $worker_id));

        if ($this->enableTask) {
            if (!$server->taskworker) {
                $server->task(1);
            }
        }
    }

    public function WorkerStop(\swoole_http_server $server, $worker_id)
    {

    }

    public function WorkerExit(\swoole_http_server $server, $worker_id)
    {

    }

    public function WorkerError(
        \swoole_http_server $server,
        $worker_id,
        $worker_pid,
        $exit_code,
        $signal
    ) {
        Logger::err(sprintf('worker[%d] error: exitCode=%s, signal=%s',
            $worker_id,
            $exit_code, $signal));
    }

    public function PipeMessage(
        \swoole_http_server $server,
        $src_worker_id,
        $message
    ) {

    }

    public function Task(
        \swoole_http_server $server,
        $task_id,
        $src_worker_id,
        $data
    ) {
        if ($src_worker_id == 1) {
            swoole_timer_tick($this->config['tick_interval_timer'] * 1000,
                function ($timer_id) {
                    Logger::debug('此处可定时清理任务');
                });
        }
    }

    public function Finish(\swoole_http_server $server, $task_id, $data)
    {

    }

    public function run()
    {
        Logger::info('运行服务...');
        $this->server->start();
    }

}