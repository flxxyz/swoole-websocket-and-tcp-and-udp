<?php

namespace swoole_websocket_and_tcp_and_udp;


use swoole_websocket_and_tcp_and_udp\protocol\HttpInterface;

interface HttpHandlerInterface extends HttpInterface
{
    function __construct();
}