<?php

namespace swoole_websocket_and_tcp_and_udp\handler;


use swoole_websocket_and_tcp_and_udp\WebsocketHandlerInterface;

class WebsocketHandler extends Handler
{
    public function __construct($handlerClass)
    {
        $this->handlerClass = $handlerClass;
    }

    public function make()
    {
        $handler = new $this->handlerClass();

        if (!($handler instanceof WebsocketHandlerInterface)) {
            throw new \Exception(sprintf('%s 当前类不属于 %s',
                $this->handlerClass, WebsocketHandlerInterface::class));
        }

        return $this->eventCallbak($handler);
    }

}