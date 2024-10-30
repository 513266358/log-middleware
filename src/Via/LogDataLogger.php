<?php

namespace Requiem\LogMiddleware\Via;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Requiem\LogMiddleware\Formatter\LineFormatter;

class LogDataLogger
{
    protected $maxFileSize; // 最大文件大小，单位为字节

    public function __construct()
    {

    }

    public function __invoke(array $config)
    {
        $logFormat = "%message%\n";
        $dateFormat = "Y-m-d";

        $handler = new RotatingFileHandler(
            $config['path'],
            0,
            $config['level'],
            true,
            $config['permission']
        );
        $handler->setFilenameFormat('{filename}-{date}', $dateFormat);
        $handler->setFormatter(new LineFormatter($logFormat, $dateFormat, true, true));

        $logPath = $config['path'];
        $logPath = str_replace(['.log'], '', $logPath);
        $filename = date($dateFormat) . '.log';
        $fullPath = $logPath . '-' . $filename;
        if (!file_exists($fullPath)) {
            touch($fullPath); // 创建新日志文件
            @fclose($fullPath);
        }

        $logger = new Logger('size_rotate', [$handler]);

        _errorLog('api_data', [], [filesize($fullPath),  $config['max_file_size']]);
        if (file_exists($fullPath) && filesize($fullPath) >= $config['max_file_size']) {
            // 超过最大文件大小，重命名并创建新文件
            $newLogPath = str_replace(['.log'], '', $fullPath);
            $newLogPath = $newLogPath . '-' . date('H-i-s') . '.log'; // 添加时间戳以确保唯一性
            rename($fullPath, $newLogPath);
            touch($fullPath); // 创建新日志文件
            @fclose($fullPath);
        }

        return $logger;
    }

}
