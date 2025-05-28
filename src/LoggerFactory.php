<?php

namespace Requiem\LogMiddleware;

use Illuminate\Support\Facades\Config;

class LoggerFactory
{
    private static array $loggers = [];

    public static function getLogger(string $service = null): Logger
    {
        // 如果没有指定服务名，则使用配置文件中的默认服务名
        $service = $service ?? Config::get('log-middleware.service');
        
        if (!isset(self::$loggers[$service])) {
            $kubernetes = [];
            if (Config::get('log-middleware.kubernetes.enabled')) {
                $kubernetes = [
                    'pod' => Config::get('log-middleware.kubernetes.pod'),
                    'namespace' => Config::get('log-middleware.kubernetes.namespace'),
                    'node' => Config::get('log-middleware.kubernetes.node'),
                    'container' => Config::get('log-middleware.kubernetes.container')
                ];
            }

            self::$loggers[$service] = new Logger(
                $service,
                Config::get('log-middleware.env'),
                Config::get('log-middleware.region'),
                $kubernetes
            );
        }
        
        return self::$loggers[$service];
    }
} 