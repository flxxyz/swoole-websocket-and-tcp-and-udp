<?php

namespace swoole_websocket_and_tcp_and_udp\protocol;

/**
 * Interface HttpInterface
 *
 * @package swoole_websocket_and_tcp_and_udp\protocol
 */
interface HttpInterface
{
    /**
     * @param \swoole_http_server   $server
     * @param \swoole_http_response $response
     *
     * @return mixed
     */
    function request(
        \swoole_http_server $server,
        \swoole_http_response $response
    );
}