<?php

namespace swoole_websocket_and_tcp_and_udp\protocol;

/**
 * Interface TcpInterface
 *
 * @package swoole_websocket_and_tcp_and_udp\protocol
 */
interface TcpInterface
{
    /**
     * @param \swoole_server $server
     * @param                $fd
     *
     * @return mixed
     */
    function connect(\swoole_server $server, $fd);

    /**
     * @param \swoole_server $server
     * @param                $fd
     * @param                $from_id
     * @param array          $data
     *
     * @return mixed
     */
    function receive(\swoole_server $server, $fd, $from_id, $data = []);

    /**
     * @param \swoole_server $server
     * @param                $fd
     *
     * @return mixed
     */
    function close(\swoole_server $server, $fd);

    /**
     * @param \swoole_server $server
     * @param                $fd
     *
     * @return mixed
     */
    function bufferFull(\swoole_server $server, $fd);

    /**
     * @param \swoole_server $server
     * @param                $fd
     *
     * @return mixed
     */
    function bufferEmpty(\swoole_server $server, $fd);
}