<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Requiem\LogMiddleware\LoggerFactory;

// 获取日志记录器（使用配置文件中的默认服务名）
$logger = LoggerFactory::getLogger();

// 记录不同级别的日志
$logger->info('服务启动成功');
$logger->warning('资源使用率超过阈值');
$logger->error('数据库连接失败');

// 记录带上下文的日志
$logger->info('用户登录成功', [
    'user_id' => 12345,
    'ip' => '192.168.1.1',
    'login_time' => date('Y-m-d H:i:s')
]); 