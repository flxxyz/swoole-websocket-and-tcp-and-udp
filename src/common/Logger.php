<?php

namespace swoole_websocket_and_tcp_and_udp\common;


trait Logger
{
    static function info($msg)
    {
        $time = date('Y-m-d H:i:s');
        echo '[' . $time . '] INFO ' . $msg . PHP_EOL;
    }

    static function debug($msg)
    {
        $time = date('Y-m-d H:i:s');
        echo '[' . $time . '] DEBUG ' . $msg . PHP_EOL;
    }

    static function warn($msg)
    {
        $time = date('Y-m-d H:i:s');
        echo '[' . $time . '] WARN ' . $msg . PHP_EOL;
    }

    static function err($msg)
    {
        $time = date('Y-m-d H:i:s');
        echo '[' . $time . '] ERROR ' . $msg . PHP_EOL;
    }
}