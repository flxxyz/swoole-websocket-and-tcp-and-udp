<?php

namespace swoole_websocket_and_tcp_and_udp_test;


use swoole_websocket_and_tcp_and_udp\common\Logger;
use swoole_websocket_and_tcp_and_udp\protocol\swoole_server;
use swoole_websocket_and_tcp_and_udp\protocol\TcpEvent;

class tcp extends TcpEvent
{
    public function connect(\swoole_server $server, $fd)
    {
        $info = $server->connection_info($fd);
        Logger::info("{$info['remote_ip']}:{$info['remote_port']}, Connect");
    }

    public function receive(\swoole_server $server, $fd, $from_id, $data = [])
    {
        $info = $server->connection_info($fd);

        if (trim($data) == 'exit') {
            $server->send($fd, '再见👋');
            $server->close($fd);
        }
        $server->send($fd, '你好呀');

        Logger::info("{$info['remote_ip']}:{$info['remote_port']}, Message [fd{$fd}]: {$data}");
    }

    public function close(\swoole_server $server, $fd)
    {
        $info = $server->connection_info($fd);
        Logger::info("{$info['remote_ip']}:{$info['remote_port']}, Close");
    }
}