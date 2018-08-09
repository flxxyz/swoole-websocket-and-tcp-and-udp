<?php

namespace swoole_websocket_and_tcp_and_udp_test;


use swoole_websocket_and_tcp_and_udp\common\Logger;
use swoole_websocket_and_tcp_and_udp\protocol\HttpEvent;

class http extends HttpEvent
{
    use Logger;

    public function __construct()
    {
    }

    public function request(
        \swoole_http_request $request,
        \swoole_http_response $response
    ) {
        $response->end('<h1>Dva爱你哟❤️</h1>');

        Logger::info("{$request->server['remote_addr']}:{$request->server['remote_port']}, Request {$request->server['request_uri']} {$request->server['request_method']} {$request->server['server_protocol']} {$request->header['user-agent']}");
    }
}