<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Requiem\LogMiddleware\LoggerFactory;

// 获取日志记录器
$logger = LoggerFactory::getLogger();

// 模拟数据库错误
try {
    throw new PDOException('数据库连接失败: Connection refused');
} catch (PDOException $e) {
    $logger->error('数据库错误', [
        'error' => [
            'code' => 'DB_CONNECTION_FAILED',
            'message' => $e->getMessage(),
            'stack' => $e->getTraceAsString()
        ],
        'context' => [
            'host' => 'db.example.com',
            'port' => 3306,
            'database' => 'orders'
        ]
    ]);
}

// 模拟业务逻辑错误
try {
    throw new RuntimeException('订单金额超出限制');
} catch (RuntimeException $e) {
    $logger->warning('业务逻辑警告', [
        'error' => [
            'code' => 'ORDER_AMOUNT_EXCEEDED',
            'message' => $e->getMessage()
        ],
        'context' => [
            'order_id' => 'ORD123456',
            'amount' => 99999.99,
            'limit' => 50000.00
        ]
    ]);
}

// 模拟系统错误
try {
    throw new Error('内存分配失败');
} catch (Error $e) {
    $logger->critical('系统错误', [
        'error' => [
            'code' => 'MEMORY_ALLOCATION_FAILED',
            'message' => $e->getMessage(),
            'stack' => $e->getTraceAsString()
        ],
        'context' => [
            'memory_limit' => ini_get('memory_limit'),
            'memory_usage' => memory_get_usage(true)
        ]
    ]);
} 