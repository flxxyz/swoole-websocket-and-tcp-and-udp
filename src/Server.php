<?php

namespace swoole_websocket_and_tcp_and_udp;


class Server
{
    protected $port;

    protected $config;

    protected $server;

    protected $primaryConfig = [];

    protected $enableWebsocket = false;

    public function __construct($config)
    {
        $this->config = $config;
        if (isset($this->config['websocket'])) {
            $this->enableWebsocket = true;
            $this->primaryConfig = $this->config['websocket'];
            $server = swoole_websocket_server::class;
        } else {
            if (isset($this->config['http'])) {
                $this->primaryConfig = $this->config['http'];
                $server = swoole_http_server::class;
            }
        }

        $host = $this->primaryConfig['host'];
        $port = $this->primaryConfig['port'];
        $type = $this->primaryConfig['type'];
        $setting = $this->primaryConfig['setting'];

        $this->server = new $server($host, $port, SWOOLE_PROCESS, $type);
        $this->server->set($setting);


        $this->bindBaseEvent();
        $this->bindHttpEvent();
        $this->bindTaskEvent();
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
        $this->server->on('WorkerError', [$this, 'WorkerError']);
        $this->server->on('PipeMessage', [$this, 'PipeMessage']);
    }

    protected function bindHttpEvent()
    {
        $this->server->on('Request', [$this, 'request']);
    }

    protected function bindTaskEvent()
    {
        $this->server->on('Task', [$this, 'task']);
        $this->server->on('Finish', [$this, 'finish']);
    }

    protected function bindWebsocketEvent()
    {
        if ($this->enableWebsocket) {
            $handlerClass = $this->primaryConfig['handler'];
            $handler = new $handlerClass();
            if (!($handler instanceof protocol\WebSocketHandlerInterface)) {
                throw new \Exception(sprintf('%s 当前类不属于 interface %s',
                    $handlerClass, protocol\WebSocketHandlerInterface::class));
            }

            $eventHandler = function ($method, array $params) use ($handler) {
                try {
                    call_user_func_array([$handler, $method], $params);
                } catch (\Exception $e) {
                    exit($e);
                }
            };

            $this->server->on('Open', function () use ($eventHandler) {
                $eventHandler('open', func_get_args());
            });

            $this->server->on('Message', function () use ($eventHandler) {
                $eventHandler('message', func_get_args());
            });

            $this->server->on('Close', function () use ($eventHandler) {
                $eventHandler('close', func_get_args());
            });
        }
    }


}