<?php

namespace App\Queue;

use App\Queue\Data\QueueDataInterface;

interface QueueInterface
{
    public function getQueueName();

    public function getQueueDataClass(): string;

    public function push(QueueDataInterface $queueData);

    public function pop();

    public function handleQueueData(QueueDataInterface $queueData);
}