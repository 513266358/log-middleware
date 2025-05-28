<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Requiem\LogMiddleware\LoggerFactory;

// 获取日志记录器
$logger = LoggerFactory::getLogger();

// 记录服务启动日志
$logger->info('服务启动', [
    'startup_time' => date('Y-m-d H:i:s'),
    'memory_usage' => memory_get_usage(true),
    'php_version' => PHP_VERSION
]);

// 记录资源使用情况
$logger->warning('资源使用警告', [
    'cpu_usage' => 85.5,
    'memory_usage' => 90.2,
    'disk_usage' => 75.8
]);

// 记录健康检查
$logger->info('健康检查', [
    'status' => 'healthy',
    'checks' => [
        'database' => 'connected',
        'redis' => 'connected',
        'api' => 'available'
    ]
]); 