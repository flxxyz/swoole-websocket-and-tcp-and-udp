<?php

namespace swoole_websocket_and_tcp_and_udp\common;


trait Swoole
{
    public function checkSwoole($version = '1.10.4')
    {
        if (!extension_loaded('swoole')) {
            throw new \Exception('swoole扩展开启失败，请检查是否启用');
        }

        //exec("php --ri swoole | grep Version | awk '{print $3}'");
        if(!version_compare(\swoole_version(), $version, '<')) {
            throw new \Exception("swoole扩展版本小于{$version}");
        }
    }
}