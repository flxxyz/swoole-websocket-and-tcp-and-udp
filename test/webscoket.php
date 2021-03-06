<?php

namespace swoole_websocket_and_tcp_and_udp_test;


use swoole_websocket_and_tcp_and_udp\common\Logger;
use swoole_websocket_and_tcp_and_udp\WebsocketHandlerInterface;

class webscoket implements WebsocketHandlerInterface
{
    use Logger;

    public function __construct()
    {
    }

    public function Open(
        \swoole_websocket_server $server,
        \swoole_http_request $request
    ) {
        $fd = $request->fd;
        $info = $server->connection_info($fd);
        Logger::info("{$info['remote_ip']}:{$info['remote_port']}, Connect");
    }

    public function Message(
        \swoole_websocket_server $server,
        \swoole_websocket_frame $frame
    ) {
        $fd = $frame->fd;
        $data = $frame->data;
        $info = $server->connection_info($fd);

        if ($data == 'exit') {
            $server->push($fd, '再见👋');
            $server->close($fd);
        }
        $server->push($fd, '你好呀');

        Logger::info("{$info['remote_ip']}:{$info['remote_port']}, Message [fd{$fd}]: {$data}");
    }

    public function Close(\swoole_websocket_server $server, $fd)
    {
        $info = $server->connection_info($fd);
        Logger::info("{$info['remote_ip']}:{$info['remote_port']}, Close");
    }
}