<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Queue;

use App\Queue\Data\AmazonOrderItemData;
use App\Queue\Data\QueueDataInterface;

class AmazonOrderQueue extends Queue
{
    public function getQueueName(): string
    {
        return 'amazon-order';
    }

    public function getQueueDataClass(): string
    {
        return AmazonOrderItemData::class;
    }

    public function handleQueueData(QueueDataInterface $queueData): bool
    {
        /**
         * @var AmazonOrderItemData $queueData
         */
        $merchant_id = $queueData->getMerchantId();
        $merchant_store_id = $queueData->getMerchantStoreId();
        $order_id = $queueData->getOrderId();



        return true;
    }

    public function safetyLine(): int
    {
        return 70;
    }
}
