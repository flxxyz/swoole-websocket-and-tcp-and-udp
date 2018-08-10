<?php

namespace swoole_websocket_and_tcp_and_udp\protocol;

/**
 * Interface PortInterface
 *
 * @package swoole_websocket_and_tcp_and_udp\protocol
 */
interface PortInterface
{
    /**
     * PortInterface constructor.
     *
     * @param \swoole_server_port $port
     */
    function __construct(\swoole_server_port $port);
}