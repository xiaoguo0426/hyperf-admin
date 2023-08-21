<?php

namespace App\Util\Log;

use App\Kernel\Log;
use DateTimeZone;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;

/**
 * Log基础类
 * Class AbstractConsoleLog
 * @package extension\Log
 * @method void log(mixed $level, string $message, array $context = [])
 * @method void debug($message, array $context = [])
 * @method void info($message, array $context = [])
 * @method void notice($message, array $context = [])
 * @method void warning($message, array $context = [])
 * @method void error($message, array $context = [])
 * @method void critical($message, array $context = [])
 * @method void alert($message, array $context = [])
 * @method void emergency($message, array $context = [])
 */
abstract class AbstractLog implements LoggerInterface
{

    public string $channel;

    private LoggerInterface $logger;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(string $channel = '', string $group = '')
    {
        $this->logger = Log::get($channel, $group);
    }

    public function __call($name, $arguments)
    {
        return $this->logger->$name(...$arguments);
    }
}