<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Queue\Data;

abstract class QueueData implements QueueDataInterface
{
    /**
     * 重试次数.
     */
    protected int $retry = 0;

    public function getRetry(): int
    {
        return $this->retry;
    }

    /**
     * @return $this
     */
    public function setRetry(int $retry): QueueData
    {
        $this->retry = $retry;
        return $this;
    }

    /**
     * @throws \JsonException
     */
    public function toArr(string $json): array
    {
        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws \Exception
     */
    public function toJson(): string
    {
        throw new \Exception('请在子类中实现 toJson 方法');
    }

    abstract public function parse(array $arr);
}
