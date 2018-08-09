<?php

namespace swoole_websocket_and_tcp_and_udp;


use swoole_websocket_and_tcp_and_udp\common\Logger;
use swoole_websocket_and_tcp_and_udp\common\ProcessTrait;
use swoole_websocket_and_tcp_and_udp\handler\HttpHandler;
use swoole_websocket_and_tcp_and_udp\handler\WebsocketHandler;

class Server
{
    use ProcessTrait, Logger;

    protected $port;

    protected $config;

    /**
     * @var \swoole_websocket_server | \swoole_http_server
     */
    protected $server;

    protected $primaryConfig = [];

    protected $enableWebsocket = false;

    protected $enableTask = false;

    public function __construct($config)
    {
        $this->config = $config;
        ini_set('date.timezone', $this->config['timezone']);

        if (isset($this->config['websocket'])) {
            $this->enableWebsocket = true;
            $this->primaryConfig = $this->config['websocket'];
            $this->primaryConfig['setting']['open_websocket_protocol'] = true;
            if(!isset($this->primaryConfig['setting']['open_http_protocol'])) {
                $this->primaryConfig['setting']['open_http_protocol'] = true;
            }
            if(!isset($this->primaryConfig['setting']['daemonize'])) {
                $this->primaryConfig['setting']['daemonize'] = false;
            }

            $serverClass = \swoole_websocket_server::class;
        } else {
            if (isset($this->config['http'])) {
                $this->primaryConfig = $this->config['http'];
                $this->primaryConfig['setting']['open_http_protocol'] = true;
                if(!isset($this->primaryConfig['setting']['daemonize'])) {
                    $this->primaryConfig['setting']['daemonize'] = false;
                }

                $serverClass = \swoole_http_server::class;
            }
        }

        $host = $this->primaryConfig['host'];
        $port = $this->primaryConfig['port'];
        $type = $this->primaryConfig['type'];
        $setting = $this->primaryConfig['setting'];

        if (isset($setting['task_worker_num'])) {
            $this->enableTask = true;
        }

        Logger::info("开始监听端口 {$host}:{$port}");
        $this->server = new $serverClass($host, $port, SWOOLE_PROCESS, $type);
        $this->server->set($setting);


        $this->bindBaseEvent();
        $this->bindTaskEvent();
        $this->bindMasterEvent();
        $this->bindOtherEvent();
    }

    /**
     * @description 此处借鉴 @laravel-s
     * @link        https://github.com/hhxsv5/laravel-s/blob/master/src/Swoole/Server.php#L65
     */
    protected function bindBaseEvent()
    {
        $this->server->on('Start', [$this, 'start']);
        $this->server->on('Shutdown', [$this, 'shutdown']);
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
                $eventHandler('open', func_get_args());
            });

            $this->server->on('Message', function () use ($eventHandler) {
                $eventHandler('message', func_get_args());
            });

            $this->server->on('Close', function () use ($eventHandler) {
                $eventHandler('close', func_get_args());
            });
        } else {
            $httpHandler = new HttpHandler($handlerClass);
            $eventHandler = $httpHandler->make();

            $this->server->on('request', function () use ($eventHandler) {
                $eventHandler('request', func_get_args());
            });
        }
    }

    protected function bindOtherEvent()
    {

    }

    public function start(\swoole_http_server $server)
    {
        foreach (spl_autoload_functions() as $function) {
            spl_autoload_unregister($function);
        }

        $this->setProcessName('master process');

        if (version_compare(swoole_version(), '1.10.4', '<')) {
            file_put_contents($this->config['pid_file'], $server->master_pid);
        }
    }

    public function shutdown(\swoole_http_server $server)
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

    public function task(
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

    public function finish(\swoole_http_server $server, $task_id, $data)
    {

    }

    public function run()
    {
        Logger::info('运行服务...');
        $this->server->start();
    }

}