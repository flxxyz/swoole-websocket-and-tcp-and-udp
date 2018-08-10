# swoole websocket and tcp and udp

## 介绍
一个基于swoole的封装程序

## 配置项

```php
return [
    'timezone'            => 'Asia/Shanghai',
    'tick_interval_timer' => 30, //秒
    'websocket'           => [
        'enable'  => true,
        'host'    => '0.0.0.0',
        'port'    => '9000',
        'type'    => SWOOLE_SOCK_TCP,
        'setting' => [
            'daemonize'       => false,
            'task_worker_num' => 1,
        ],
        'handler' => \swoole_websocket_and_tcp_and_udp_test\webscoket::class,
    ],
    'http'                => [
        'enable'  => true,
        'host'    => '0.0.0.0',
        'port'    => '9001',
        'type'    => SWOOLE_SOCK_TCP,
        'setting' => [],
        'handler' => \swoole_websocket_and_tcp_and_udp_test\http::class,
    ],
    'tcp'                 => [
        'enable'  => true,
        'host'    => '0.0.0.0',
        'port'    => '9002',
        'type'    => SWOOLE_SOCK_TCP,
        'setting' => [
            'open_eof_check'           => true,
            'package_eof'              => "\r\n",
            'dispatch_mode'            => 2,
            'heartbeat_check_interval' => 30,
            'heartbeat_idle_time'      => 60,
        ],
        'handler' => \swoole_websocket_and_tcp_and_udp_test\tcp::class,
    ],
    'udp'                 => [
        'enable'  => true,
        'host'    => '0.0.0.0',
        'port'    => '9003',
        'type'    => SWOOLE_SOCK_UDP,
        'setting' => [
            'open_eof_check' => true,
            'package_eof'    => "\r\n",
        ],
        'handler' => '',
    ],
];
```

## 任务
- [x] 监听websocket
- [x] 监听http
- [x] 监听tcp
- [x] 监听udp
- [x] 混合监听

## 感谢
[laravel-s](https://github.com/hhxsv5/laravel-s)项目给予很大的灵感，谢谢
