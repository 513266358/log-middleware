<?php

namespace Requiem\LogMiddleware;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Illuminate\Support\Facades\Queue;
use Requiem\LogMiddleware\Jobs\ProcessLog;

class Logger implements LoggerInterface
{
    private string $service;
    private string $env;
    private string $region;
    private array $kubernetes = [];
    private array $sensitiveFields = ['password', 'token', 'cvv', 'authorization'];
    
    // 日志级别定义
    private array $levelConfig = [
        LogLevel::INFO => [
            'storage_days' => 7,
            'alert' => false
        ],
        LogLevel::WARNING => [
            'storage_days' => 30,
            'alert' => 'wechat'
        ],
        LogLevel::ERROR => [
            'storage_days' => -1, // 永久存储
            'alert' => 'pagerduty'
        ]
    ];

    public function __construct(
        string $service,
        string $env,
        string $region,
        array $kubernetes = []
    ) {
        $this->service = $service;
        $this->env = $env;
        $this->region = $region;
        $this->kubernetes = $kubernetes;
    }

    public function emergency($message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function alert($message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical($message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function error($message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function warning($message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function notice($message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function info($message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function debug($message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    public function log($level, $message, array $context = []): void
    {
        if (!isset($this->levelConfig[$level])) {
            throw new \InvalidArgumentException('Invalid log level: ' . $level);
        }

        $logData = $this->formatLogData($level, $message, $context);
        $this->dispatchLog($logData);
    }

    private function formatLogData(string $level, $message, array $context): array
    {
        $logData = [
            'timestamp' => gmdate('c'), // ISO8601 format
            'level' => $level,
            'service' => $this->service,
            'trace_id' => $this->generateTraceId(),
            'env' => $this->env,
            'region' => $this->region,
            'message' => $message,
            'storage_days' => $this->levelConfig[$level]['storage_days'],
            'alert_type' => $this->levelConfig[$level]['alert']
        ];

        if (!empty($this->kubernetes)) {
            $logData['kubernetes'] = $this->kubernetes;
        }

        // 处理 HTTP 上下文
        if (isset($context['http'])) {
            $logData['http'] = $this->formatHttpContext($context['http']);
        }

        // 处理错误信息
        if (in_array($level, [LogLevel::ERROR, LogLevel::WARNING]) && isset($context['error'])) {
            $logData['error'] = $this->formatErrorContext($context['error']);
        }

        // 添加其他上下文
        $otherContext = array_diff_key($context, ['http' => 1, 'error' => 1]);
        if (!empty($otherContext)) {
            $logData['context'] = $this->sanitizeData($otherContext);
        }

        return $logData;
    }

    private function formatHttpContext(array $http): array
    {
        $formatted = [
            'method' => $http['method'] ?? null,
            'path' => $http['path'] ?? null,
            'status_code' => $http['status_code'] ?? null,
        ];

        if (isset($http['params'])) {
            $formatted['params'] = [
                'path' => $this->sanitizeData($http['params']['path'] ?? []),
                'query' => $this->sanitizeData($http['params']['query'] ?? []),
                'body' => $this->sanitizeData($http['params']['body'] ?? [])
            ];
        }

        if (isset($http['headers'])) {
            $formatted['headers'] = $this->sanitizeHeaders($http['headers']);
        }

        return $formatted;
    }

    private function formatErrorContext(array $error): array
    {
        $formatted = [
            'code' => $error['code'] ?? null,
            'message' => $error['message'] ?? null
        ];

        if (isset($error['stack'])) {
            $formatted['stack'] = $error['stack'];
        }

        return $formatted;
    }

    private function sanitizeData(array $data): array
    {
        $sanitized = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeData($value);
            } else {
                $sanitized[$key] = $this->isSensitiveField($key) ? '******' : $value;
            }
        }
        return $sanitized;
    }

    private function sanitizeHeaders(array $headers): array
    {
        $sanitized = [];
        foreach ($headers as $key => $value) {
            $sanitized[$key] = $this->isSensitiveField($key) ? '******' : $value;
        }
        return $sanitized;
    }

    private function isSensitiveField(string $field): bool
    {
        $field = strtolower($field);
        foreach ($this->sensitiveFields as $sensitive) {
            if (strpos($field, $sensitive) !== false) {
                return true;
            }
        }
        return false;
    }

    private function generateTraceId(): string
    {
        return sprintf(
            '00-%s-%s-01',
            bin2hex(random_bytes(16)),
            bin2hex(random_bytes(8))
        );
    }

    private function dispatchLog(array $logData): void
    {
        Queue::push(new ProcessLog($logData));
    }
} 