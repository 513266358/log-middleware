<?php

namespace Requiem\LogMiddleware\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $logData;

    public function __construct(array $logData)
    {
        $this->logData = $logData;
    }

    public function handle(): void
    {
        // 获取日志文件路径
        $logFile = $this->getLogFilePath();
        
        // 确保目录存在
        $this->ensureDirectoryExists($logFile);
        
        // 写入日志
        $this->writeLog($logFile);
        
        // 处理告警
        $this->handleAlert();
    }

    private function getLogFilePath(): string
    {
        $date = date('Y-m-d');
        return sprintf(
            'logs/%s/%s/%s/%s.log',
            $this->logData['env'],
            $this->logData['service'],
            $date,
            $this->logData['service']
        );
    }

    private function ensureDirectoryExists(string $logFile): void
    {
        $directory = dirname($logFile);
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }
    }

    private function writeLog(string $logFile): void
    {
        $logContent = json_encode($this->logData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
        Storage::append($logFile, $logContent);
    }

    private function handleAlert(): void
    {
        if (empty($this->logData['alert_type'])) {
            return;
        }

        switch ($this->logData['alert_type']) {
            case 'wechat':
                $this->sendWechatAlert();
                break;
            case 'pagerduty':
                $this->sendPagerDutyAlert();
                break;
        }
    }

    private function sendWechatAlert(): void
    {
        // 实现企业微信告警
        // TODO: 实现企业微信告警逻辑
    }

    private function sendPagerDutyAlert(): void
    {
        // 实现 PagerDuty 告警
        // TODO: 实现 PagerDuty 告警逻辑
    }
} 