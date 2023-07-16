<?php

namespace App\Queue;

use App\Kernel\Redis;
use App\Queue\Data\QueueDataInterface;
use App\Util\Prefix;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

abstract class AbstractQueue
{
    protected ?object $redis;
    protected string $queue_name;

    protected int $timeout = 10;
    protected int $retryInterval = 10;

    /**
     * 是否记录队列数据处理耗时
     * @var bool
     */
    protected bool $isLogHandleDataTime = false;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct()
    {
        $this->queue_name = Prefix::queue() . $this->getQueueName();
        $this->redis = Redis::get();
    }

    abstract public function getQueueName();

    abstract public function getQueueDataClass(): string;

    abstract public function push(QueueDataInterface $queueData);

    abstract public function pop();

    abstract public function handleQueueData(QueueDataInterface $queueData);
}