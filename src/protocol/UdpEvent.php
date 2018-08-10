<?php

namespace swoole_websocket_and_tcp_and_udp\protocol;

/**
 * Class UdpEvent
 *
 * @package swoole_websocket_and_tcp_and_udp\protocol
 */
abstract class UdpEvent implements UdpInterface, PortInterface
{
    /**
     * @var \swoole_server_port
     */
    protected $port;

    /**
     * UdpEvent constructor.
     *
     * @param \swoole_server_port $port
     */
    public function __construct(\swoole_server_port $port)
    {
        $this->port = $port;
    }

    public function packet(\swoole_server $server, $data, $clientInfo)
    {
        // TODO: Implement packet() method.
    }
}