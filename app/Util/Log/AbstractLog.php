<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Log;

use App\Kernel\Log;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;

/**
 * Log基础类
 * Class AbstractConsoleLog.
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
abstract class AbstractLog
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
        return $this->logger->{$name}(...$arguments);
    }
}
