<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util;

use Hyperf\Contract\StdoutLoggerInterface;

/**
 * Class ConsoleLog.
 * @method void info($message)
 * @method void error($message)
 * @method void comment($message)
 * @method void warning($message)
 * @method void highlight($message)
 */
class ConsoleLog
{
    protected StdoutLoggerInterface $logger;

    public function __construct(StdoutLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __call($name, $arguments)
    {
        return \Hyperf\Support\env('DEBUG') !== true ? $this->logger->{$name}(...$arguments) : null;
    }
}
