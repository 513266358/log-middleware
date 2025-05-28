<?php

namespace Requiem\LogMiddleware;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\App;

class LoggerFactory
{
    private static array $loggers = [];
    private static bool $configLoaded = false;

    private static function loadConfig(): void
    {
        if (!self::$configLoaded) {
            // 检查配置文件是否存在
            $configPath = __DIR__ . '/../config/log-middleware.php';
            if (file_exists($configPath)) {
                // 如果使用 Laravel，使用 Laravel 的配置系统
                if (class_exists('Illuminate\Support\Facades\Config')) {
                    Config::set('log-middleware', require $configPath);
                } else {
                    // 如果不使用 Laravel，直接加载配置
                    $config = require $configPath;
                    self::$config = $config;
                }
            } else {
                // 如果配置文件不存在，使用默认配置
                self::$config = [
                    'service' => getenv('APP_NAME') ?: 'unknown-service',
                    'env' => getenv('APP_ENV') ?: 'production',
                    'region' => getenv('APP_REGION') ?: 'us-east1',
                    'kubernetes' => [
                        'enabled' => false,
                        'pod' => '',
                        'namespace' => '',
                        'node' => '',
                        'container' => ''
                    ]
                ];
            }
            self::$configLoaded = true;
        }
    }

    private static function getConfig(string $key, $default = null)
    {
        self::loadConfig();
        
        if (class_exists('Illuminate\Support\Facades\Config')) {
            return Config::get('log-middleware.' . $key, $default);
        }
        
        return self::$config[$key] ?? $default;
    }

    public static function getLogger(string $service = null): Logger
    {
        // 如果没有指定服务名，则使用配置文件中的默认服务名
        $service = $service ?? self::getConfig('service', 'unknown-service');
        
        if (!isset(self::$loggers[$service])) {
            $kubernetes = [];
            if (self::getConfig('kubernetes.enabled', false)) {
                $kubernetes = [
                    'pod' => self::getConfig('kubernetes.pod', ''),
                    'namespace' => self::getConfig('kubernetes.namespace', ''),
                    'node' => self::getConfig('kubernetes.node', ''),
                    'container' => self::getConfig('kubernetes.container', '')
                ];
            }

            self::$loggers[$service] = new Logger(
                $service,
                self::getConfig('env', 'production'),
                self::getConfig('region', 'us-east1'),
                $kubernetes
            );
        }
        
        return self::$loggers[$service];
    }
} 