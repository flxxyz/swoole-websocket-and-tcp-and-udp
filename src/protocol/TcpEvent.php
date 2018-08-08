<?php

namespace swoole_websocket_and_tcp_and_udp\protocol;

/**
 * Class TcpEvent
 *
 * @package swoole_websocket_and_tcp_and_udp\protocol
 */
abstract class TcpEvent implements TcpInterface
{
    /**
     * @var \swoole_server_port
     */
    protected $port;

    /**
     * TcpEvent constructor.
     *
     * @param \swoole_server_port $port
     */
    public function __construct(\swoole_server_port $port)
    {
        $this->port = $port;
    }

    public function connect(\swoole_server $server, $fd)
    {
        // TODO: Implement connect() method.
    }

    public function receive(swoole_server $server, $fd, $from_id, $data = [])
    {
        // TODO: Implement receive() method.
    }

    public function close(swoole_server $server, $fd)
    {
        // TODO: Implement close() method.
    }

    public function bufferFull(swoole_server $server, $fd)
    {
        // TODO: Implement bufferFull() method.
    }

    public function bufferEmpty(swoole_server $server, $fd)
    {
        // TODO: Implement bufferEmpty() method.
    }
}