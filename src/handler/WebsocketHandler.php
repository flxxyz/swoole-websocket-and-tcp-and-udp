<?php

namespace swoole_websocket_and_tcp_and_udp\handler;


use swoole_websocket_and_tcp_and_udp\protocol\WebsocketEvent;

class WebsocketHandler extends Handler
{
    public function __construct($handlerClass)
    {
        $this->handlerClass = $handlerClass;
    }

    public function make()
    {
        $handler = new $this->handlerClass();

        if (!($handler instanceof WebsocketEvent)) {
            throw new \Exception(sprintf('%s 当前类不属于 %s',
                $this->handlerClass, WebsocketEvent::class));
        }

        return $this->eventCallbak($handler);
    }

}