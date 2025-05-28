<?php

return [
    /*
    |--------------------------------------------------------------------------
    | 日志中间件配置
    |--------------------------------------------------------------------------
    |
    | 这里包含了日志中间件所需的所有配置信息
    |
    */

    // 服务名称，从环境变量获取
    'service' => env('APP_NAME', 'unknown-service'),

    // 环境名称，从环境变量获取
    'env' => env('APP_ENV', 'production'),

    // 区域设置
    'region' => env('APP_REGION', 'us-east1'),

    // Kubernetes配置
    'kubernetes' => [
        'enabled' => env('KUBERNETES_ENABLED', false),
        'pod' => env('KUBERNETES_POD_NAME', ''),
        'namespace' => env('KUBERNETES_NAMESPACE', ''),
        'node' => env('KUBERNETES_NODE_NAME', ''),
        'container' => env('KUBERNETES_CONTAINER_NAME', ''),
    ],

    // 日志存储配置
    'storage' => [
        'path' => storage_path('logs'),
        'retention' => [
            'info' => 7,      // INFO级别日志保留天数
            'warning' => 30,  // WARNING级别日志保留天数
            'error' => -1,    // ERROR级别日志永久保留
        ],
    ],

    // 告警配置
    'alerts' => [
        'wechat' => [
            'enabled' => env('WECHAT_ALERT_ENABLED', false),
            'webhook' => env('WECHAT_WEBHOOK_URL', ''),
        ],
        'pagerduty' => [
            'enabled' => env('PAGERDUTY_ALERT_ENABLED', false),
            'service_key' => env('PAGERDUTY_SERVICE_KEY', ''),
        ],
    ],
]; 