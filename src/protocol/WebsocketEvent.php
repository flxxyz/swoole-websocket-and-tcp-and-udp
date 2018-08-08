<?php

namespace swoole_websocket_and_tcp_and_udp\protocol;


abstract class WebsocketEvent implements WebsocketInterface
{
    /**
     * @var \swoole_server_port
     */
    protected $port;

    /**
     * Websocket constructor.
     *
     * @param \swoole_server_port $port
     */
    public function __construct(\swoole_server_port $port)
    {
        $this->port = $port;
    }

    public function open(
        \swoole_websocket_server $server,
        \swoole_http_request $request
    ) {
        // TODO: Implement open() method.
    }

    public function message(
        \swoole_websocket_server $server,
        \swoole_websocket_frame $frame
    ) {
        // TODO: Implement message() method.
    }

    public function close(\swoole_websocket_server $server, $fd)
    {
        // TODO: Implement close() method.
    }
}