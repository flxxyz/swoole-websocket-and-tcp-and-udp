<?php

namespace swoole_websocket_and_tcp_and_udp\protocol;

/**
 * Interface WebsocketInterface
 *
 * @package swoole_websocket_and_tcp_and_udp\protocol
 */
interface WebsocketInterface
{
    /**
     * @param \swoole_websocket_server $server
     * @param \swoole_http_request     $request
     *
     * @return mixed
     */
    function open(
        \swoole_websocket_server $server,
        \swoole_http_request $request
    );

    /**
     * @param \swoole_websocket_server $server
     * @param \swoole_websocket_frame  $frame
     *
     * @return mixed
     */
    function message(
        \swoole_websocket_server $server,
        \swoole_websocket_frame $frame
    );

    /**
     * @param \swoole_websocket_server $server
     * @param                          $fd
     *
     * @return mixed
     */
    function close(\swoole_websocket_server $server, $fd);
}