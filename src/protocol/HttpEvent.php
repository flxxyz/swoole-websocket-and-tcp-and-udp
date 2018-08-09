<?php

namespace swoole_websocket_and_tcp_and_udp\protocol;

/**
 * Class HttpEvent
 *
 * @package swoole_websocket_and_tcp_and_udp\protocol
 */
abstract class HttpEvent implements HttpInterface
{
    /**
     * @var \swoole_server_port
     */
    protected $port;

    /**
     * HttpEvent constructor.
     *
     * @param \swoole_server_port $port
     */
    public function __construct(\swoole_server_port $port)
    {
        $this->port = $port;
    }

    public function request(
        \swoole_http_request $request,
        \swoole_http_response $response
    ) {
        // TODO: Implement request() method.
    }
}