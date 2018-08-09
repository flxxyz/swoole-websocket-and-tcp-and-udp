<?php

namespace swoole_websocket_and_tcp_and_udp\common;


trait ProcessTrait
{
    public function setProcessName($name)
    {
        if (PHP_OS == 'Darwin') {
            return;
        }

        if (function_exists('\swoole_set_process_name')) {
            \swoole_set_process_name($name);
        }
    }
}