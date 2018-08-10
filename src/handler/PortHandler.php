<?php

namespace swoole_websocket_and_tcp_and_udp\handler;


use swoole_websocket_and_tcp_and_udp\protocol\HttpEvent;
use swoole_websocket_and_tcp_and_udp\protocol\PortInterface;
use swoole_websocket_and_tcp_and_udp\protocol\WebsocketEvent;

abstract class PortHandler
{
    protected $port;

    protected $handlerClass;

    protected function make()
    {
    }

    /**
     * @param \swoole_server_port $port
     * @param                     $handlerClass
     *
     * @return mixed
     * @throws \Exception
     */
    protected function eventCallbak(\swoole_server_port $port, $handlerClass)
    {
        static $handlers = [];
        $portHash = spl_object_hash($port);
        if (isset($handlers[$portHash])) {
            return $handlers[$portHash];
        }
        $t = new $handlerClass($port);
        if (!($t instanceof PortInterface)) {
            throw new \Exception(sprintf('%s must extend the abstract class TcpSocket/UdpSocket',
                $this->handlerClass));
        }
        $handlers[$portHash] = $t;

        return $handlers[$portHash];
    }
}