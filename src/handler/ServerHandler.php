<?php

namespace swoole_websocket_and_tcp_and_udp\handler;


class ServerHandler extends PortHandler
{
    public function __construct(\swoole_server_port $port, $handlerClass)
    {
        $this->port = $port;
        $this->handlerClass = $handlerClass;
    }

    public function make()
    {
        return function ($method, $params) {
            $handler = $this->eventCallbak($this->port, $this->handlerClass);
            if (method_exists($handler, $method)) {
                try {
                    call_user_func_array([$handler, $method], $params);
                } catch (\Exception $e) {
                    exit($e);
                }
            }
        };
    }
}