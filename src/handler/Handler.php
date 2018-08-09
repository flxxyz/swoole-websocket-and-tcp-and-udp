<?php

namespace swoole_websocket_and_tcp_and_udp\handler;


use swoole_websocket_and_tcp_and_udp\protocol\HttpEvent;
use swoole_websocket_and_tcp_and_udp\protocol\WebsocketEvent;

abstract class Handler
{
    protected function make()
    {
    }

    /**
     * @param WebsocketEvent | HttpEvent $handler
     *
     * @return \Closure
     */
    protected function eventCallbak($handler)
    {
        return function ($method, $params) use ($handler) {
            try {
                call_user_func_array([$handler, $method], $params);
            } catch (\Exception $e) {
                exit($e);
            }
        };
    }
}