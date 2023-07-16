<?php

namespace App\Queue\Data;

use Exception;
use JsonException;

abstract class QueueData implements QueueDataInterface
{
    /**
     * 重试次数
     * @var int
     */
    protected int $retry = 0;

    /**
     * @return int
     */
    public function getRetry(): int
    {
        return $this->retry;
    }

    /**
     * @param int $retry
     * @return $this
     */
    public function setRetry(int $retry): QueueData
    {
        $this->retry = $retry;
        return $this;
    }

    /**
     * @throws JsonException
     */
    public function toArr(string $json): array
    {
        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws Exception
     */
    public function toJson(): string
    {
        throw new Exception('请在子类中实现 toJson 方法');
    }

    abstract public function parse(array $arr);
}