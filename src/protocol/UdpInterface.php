<?php

namespace swoole_websocket_and_tcp_and_udp\protocol;

/**
 * Interface UdpInterface
 *
 * @package swoole_websocket_and_tcp_and_udp\protocol
 */
interface UdpInterface
{
    /**
     * @param \swoole_server $server
     * @param                $data
     * @param                $clientInfo
     *
     * @return mixed
     */
    function packet(\swoole_server $server, $data, $clientInfo);
}