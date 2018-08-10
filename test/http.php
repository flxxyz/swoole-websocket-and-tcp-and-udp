<?php

namespace swoole_websocket_and_tcp_and_udp_test;


use swoole_websocket_and_tcp_and_udp\common\Logger;
use swoole_websocket_and_tcp_and_udp\HttpHandlerInterface;
use swoole_websocket_and_tcp_and_udp\protocol\HttpEvent;

class http extends HttpEvent
{
    use Logger;

    public function Request(
        \swoole_http_request $request,
        \swoole_http_response $response
    ) {
        $html = <<<EOT
            <title>https://github.com/flxxyz/swoole-websocket-and-tcp-and-udp</title>
            <h1><a href="https://github.com/flxxyz/swoole-websocket-and-tcp-and-udp" target="_blank">Dva爱你哟❤️</a></h1>
EOT;

        $response->end($html);

        Logger::info("{$request->server['remote_addr']}:{$request->server['remote_port']}, Request {$request->server['request_uri']} {$request->server['request_method']} {$request->server['server_protocol']} {$request->header['user-agent']}");
    }
}