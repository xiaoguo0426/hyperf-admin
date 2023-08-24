<?php

namespace App\Util;

use App\Util\Log\AbstractLog;
use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * 多平台日志基础类
 * Class AbstractConsoleLog
 * @package extension\Log
 */
class MultiLog implements LoggerInterface
{

    /**
     * @var AbstractLog[]|StdoutLoggerInterface[] $loggers
     */
    private array $loggers;

    public function register($logger): self
    {
        $this->loggers[] = $logger;
        return $this;
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


    public function log($level, string|\Stringable $message, array $context = []): void
    {
        foreach ($this->loggers as $logger) {
            $logger->log($level, $message, $context);
        }
    }

}