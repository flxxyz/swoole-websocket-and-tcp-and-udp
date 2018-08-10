<?php

namespace swoole_websocket_and_tcp_and_udp;


use swoole_websocket_and_tcp_and_udp\protocol\WebsocketInterface;

interface WebsocketHandlerInterface extends WebsocketInterface
{
    function __construct();
}