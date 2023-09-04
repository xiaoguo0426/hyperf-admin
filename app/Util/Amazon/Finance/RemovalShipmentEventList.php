<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Finance;

use AmazonPHP\SellingPartner\Model\Finances\RemovalShipmentEvent;
use Hyperf\Collection\Collection;

class RemovalShipmentEventList extends FinanceBase
{
    public function run($financialEvents): bool
    {
        $collection = new Collection();
        /**
         * @var RemovalShipmentEvent $financialEvent
         */
        foreach ($financialEvents as $financialEvent) {
            $postedDate = $financialEvent->getPostedDate();
            $posted_date = '';
            if (! is_null($postedDate)) {
                $posted_date = $postedDate->format('Y-m-d H:i:s');
            }
            $merchant_order_id = $financialEvent->getMerchantOrderId() ?? '';
            $order_id = $financialEvent->getOrderId() ?? '';
            $transaction_type = $financialEvent->getTransactionType() ?? '';
            $removalShipmentItemList = $financialEvent->getRemovalShipmentItemList();
            $removal_shipment_item_list = [];
            if (! is_null($removalShipmentItemList)) {
                foreach ($removalShipmentItemList as $removalShipmentItem) {
                    $removal_shipment_item_id = $removalShipmentItem->getRemovalShipmentItemId() ?? '';
                    $tax_collection_model = $removalShipmentItem->getTaxCollectionModel() ?? '';
                    $fulfillment_network_sku = $removalShipmentItem->getFulfillmentNetworkSku() ?? '';
                    $quantity = $removalShipmentItem->getQuantity() ?? 0;
                    $revenue = $removalShipmentItem->getRevenue();
                    $revenue_currency = '';
                    $revenue_amount = 0.00;
                    if (! is_null($revenue)) {
                        $revenue_currency = $revenue->getCurrencyCode() ?? '';
                        $revenue_amount = $revenue->getCurrencyAmount() ?? 0.00;
                    }
                    $feeAmount = $removalShipmentItem->getFeeAmount();
                    $fee_amount_currency = '';
                    $fee_amount = 0.00;
                    if (! is_null($feeAmount)) {
                        $fee_amount_currency = $feeAmount->getCurrencyCode() ?? '';
                        $fee_amount = $feeAmount->getCurrencyAmount() ?? 0.00;
                    }
                    $taxAmount = $removalShipmentItem->getTaxAmount();
                    $tax_amount_currency = '';
                    $tax_amount = 0.00;
                    if (! is_null($taxAmount)) {
                        $tax_amount_currency = $taxAmount->getCurrencyCode() ?? '';
                        $tax_amount = $taxAmount->getCurrencyAmount() ?? 0.00;
                    }
                    $taxWithheld = $removalShipmentItem->getTaxWithheld();
                    $tax_withheld_currency = '';
                    $tax_withheld_amount = 0.00;
                    if (! is_null($taxWithheld)) {
                        $tax_withheld_currency = $taxWithheld->getCurrencyCode() ?? '';
                        $tax_withheld_amount = $taxWithheld->getCurrencyAmount() ?? 0.00;
                    }

                    $removal_shipment_item_list[] = [
                        'removal_shipment_item_id' => $removal_shipment_item_id,
                        'tax_collection_model' => $tax_collection_model,
                        'fulfillment_network_sku' => $fulfillment_network_sku,
                        'quantity' => $quantity,
                        'revenue_currency' => $revenue_currency,
                        'revenue_amount' => $revenue_amount,
                        'fee_amount_currency' => $fee_amount_currency,
                        'fee_amount' => $fee_amount,
                        'tax_amount_currency' => $tax_amount_currency,
                        'tax_amount' => $tax_amount,
                        'tax_withheld_currency' => $tax_withheld_currency,
                        'tax_withheld_amount' => $tax_withheld_amount,
                    ];
                }
            }

            $collection->push([
                'merchant_id' => $this->merchant_id,
                'merchant_store_id' => $this->merchant_store_id,
                'posted_date' => $posted_date,
                'merchant_order_id' => $merchant_order_id,
                'order_id' => $order_id,
                'transaction_type' => $transaction_type,
                'removal_shipment_item_list' => $removal_shipment_item_list,
            ]);
        }
        return true;
    }
}
