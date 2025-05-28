<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Requiem\LogMiddleware\LoggerFactory;

// 获取日志记录器
$logger = LoggerFactory::getLogger();

// 模拟 HTTP 请求日志
$logger->info('处理订单请求', [
    'http' => [
        'method' => 'POST',
        'path' => '/api/v1/orders',
        'status_code' => 201,
        'params' => [
            'path' => ['orderId' => 'ORD123456'],
            'query' => ['region' => 'eu-west'],
            'body' => [
                'amount' => 299.99,
                'card' => '****1234',
                'customer' => [
                    'name' => '张三',
                    'email' => 'zhangsan@example.com'
                ]
            ]
        ],
        'headers' => [
            'user-agent' => 'Mobile/15E148 Safari/604.1',
            'x-api-version' => '2.3',
            'authorization' => 'Bearer ******'
        ]
    ]
]);

// 模拟错误响应
$logger->error('支付处理失败', [
    'http' => [
        'method' => 'POST',
        'path' => '/api/v1/payments',
        'status_code' => 400,
        'params' => [
            'body' => [
                'order_id' => 'ORD123456',
                'amount' => 299.99
            ]
        ]
    ],
    'error' => [
        'code' => 'PAYMENT_FAILED',
        'message' => '信用卡授权失败',
        'stack' => 'at PaymentProcessor->authorize() line 89'
    ]
]); 