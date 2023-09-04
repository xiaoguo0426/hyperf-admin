<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Queue\Data;

interface QueueDataInterface
{
    public function toArr(string $json): array;

    public function toJson(): string;

    public function parse(array $arr);
}
