<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Queue;

use AmazonPHP\SellingPartner\AccessToken;
use AmazonPHP\SellingPartner\Exception\ApiException;
use AmazonPHP\SellingPartner\SellingPartnerSDK;
use App\Queue\Data\AmazonOrderItemData;
use App\Queue\Data\QueueDataInterface;
use App\Util\Amazon\OrderItemCreator;
use App\Util\Amazon\OrderItemEngine;
use App\Util\AmazonApp;
use App\Util\AmazonSDK;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;

class AmazonOrderItemQueue extends Queue
{
    public function getQueueName(): string
    {
        return 'amazon-order-item';
    }

    public function getQueueDataClass(): string
    {
        return AmazonOrderItemData::class;
    }

    /**
     * @param QueueDataInterface $queueData
     * @throws ApiException
     * @throws JsonException
     * @throws ClientExceptionInterface
     * @return bool
     */
    public function handleQueueData(QueueDataInterface $queueData): bool
    {
        /**
         * @var AmazonOrderItemData $queueData
         */
        $merchant_id = $queueData->getMerchantId();
        $merchant_store_id = $queueData->getMerchantStoreId();
        $amazon_order_ids = $queueData->getOrderId();

        AmazonApp::tok($merchant_id, $merchant_store_id, static function (AmazonSDK $amazonSDK, int $merchant_id, int $merchant_store_id, SellingPartnerSDK $sdk, AccessToken $accessToken, string $region, array $marketplace_ids) use ($amazon_order_ids) {

            $orderItemCreator = new OrderItemCreator();
            $orderItemCreator->setAmazonOrderIds($amazon_order_ids);
            \Hyperf\Support\make(OrderItemEngine::class)->launch($amazonSDK, $sdk, $accessToken, $orderItemCreator);

            return true;
        });

        return true;
    }

    public function safetyLine(): int
    {
        return 70;
    }
}
