<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util;

class RuntimeCalculator
{
    private float|string $startTime;

    public function start(): void
    {
        $this->startTime = microtime(true);
    }

    public function stop(): float
    {
        return round(microtime(true) - $this->startTime, 3);
    }
}
