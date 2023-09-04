<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Finance;

use AmazonPHP\SellingPartner\Model\Finances\RemovalShipmentAdjustmentEvent;
use Hyperf\Collection\Collection;

class RemovalShipmentAdjustmentEventList extends FinanceBase
{
    public function run($financialEvents): bool
    {
        $collection = new Collection();
        /**
         * @var RemovalShipmentAdjustmentEvent $financialEvent
         */
        foreach ($financialEvents as $financialEvent) {
            $postedDate = $financialEvent->getPostedDate();
            $posted_date = '';
            if (! is_null($postedDate)) {
                $posted_date = $postedDate->format('Y-m-d H:i:s');
            }
            $adjustment_event_id = $financialEvent->getAdjustmentEventId();
            $merchant_order_id = $financialEvent->getMerchantOrderId() ?? '';
            $order_id = $financialEvent->getOrderId() ?? '';
            $transaction_type = $financialEvent->getTransactionType() ?? '';
            $removalShipmentItemAdjustmentList = $financialEvent->getRemovalShipmentItemAdjustmentList();
            $removal_shipment_item_adjustment_list = [];
            if (! is_null($removalShipmentItemAdjustmentList)) {
                foreach ($removalShipmentItemAdjustmentList as $removalShipmentItemAdjustment) {
                    $removal_shipment_item_id = $removalShipmentItemAdjustment->getRemovalShipmentItemId() ?? '';
                    $tax_collection_model = $removalShipmentItemAdjustment->getTaxCollectionModel() ?? '';
                    $fulfillment_network_sku = $removalShipmentItemAdjustment->getFulfillmentNetworkSku() ?? '';
                    $adjustment_quantity = $removalShipmentItemAdjustment->getAdjustedQuantity() ?? 0;
                    $revenueAdjustment = $removalShipmentItemAdjustment->getRevenueAdjustment();
                    $revenue_adjustment_currency = '';
                    $revenue_adjustment_amount = '';
                    if (! is_null($revenueAdjustment)) {
                        $revenue_adjustment_currency = $revenueAdjustment->getCurrencyCode() ?? '';
                        $revenue_adjustment_amount = $revenueAdjustment->getCurrencyAmount() ?? 0.00;
                    }
                    $taxAmountAdjustment = $removalShipmentItemAdjustment->getTaxAmountAdjustment();
                    $tax_amount_adjustment_currency = '';
                    $tax_amount_adjustment_amount = '';
                    if (! is_null($taxAmountAdjustment)) {
                        $tax_amount_adjustment_currency = $taxAmountAdjustment->getCurrencyCode() ?? '';
                        $tax_amount_adjustment_amount = $taxAmountAdjustment->getCurrencyAmount() ?? 0.00;
                    }
                    $taxWithheldAdjustment = $removalShipmentItemAdjustment->getTaxWithheldAdjustment();
                    $tax_withheld_adjustment_currency = '';
                    $tax_withheld_adjustment_amount = '';
                    if (! is_null($taxWithheldAdjustment)) {
                        $taxWithheldAdjustment->getCurrencyCode();
                        $taxWithheldAdjustment->getCurrencyAmount();
                    }

                    $removal_shipment_item_adjustment_list[] = [
                        'removal_shipment_item_id' => $removal_shipment_item_id,
                        'tax_collection_model' => $tax_collection_model,
                        'fulfillment_network_sku' => $fulfillment_network_sku,
                        'adjustment_quantity' => $adjustment_quantity,
                        'revenue_adjustment_currency' => $revenue_adjustment_currency,
                        'revenue_adjustment_amount' => $revenue_adjustment_amount,
                        'tax_amount_adjustment_currency' => $tax_amount_adjustment_currency,
                        'tax_amount_adjustment_amount' => $tax_amount_adjustment_amount,
                        'tax_withheld_adjustment_currency' => $tax_withheld_adjustment_currency,
                        'tax_withheld_adjustment_amount' => $tax_withheld_adjustment_amount,
                    ];
                }
            }

            $collection->push([
                'merchant_id' => $this->merchant_id,
                'merchant_store_id' => $this->merchant_store_id,
                'posted_date' => $posted_date,
                '$adjustment_event_id' => $adjustment_event_id,
                'merchant_order_id' => $merchant_order_id,
                'order_id' => $order_id,
                'transaction_type' => $transaction_type,
                'removal_shipment_item_adjustment_list' => $removal_shipment_item_adjustment_list,
            ]);
        }
        return true;
    }
}
