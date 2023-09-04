<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Finance;

use AmazonPHP\SellingPartner\Model\Finances\AdjustmentEvent;
use Hyperf\Collection\Collection;

class AdjustmentEventList extends FinanceBase
{
    public function run($financialEvents): bool
    {
        $collection = new Collection();
        /**
         * @var AdjustmentEvent $financialEvent
         */
        foreach ($financialEvents as $financialEvent) {
            $adjustment_type = $financialEvent->getAdjustmentType() ?? '';
            $postedDate = $financialEvent->getPostedDate();
            $posted_date = '';
            if (! is_null($postedDate)) {
                $posted_date = $postedDate->format('Y-m-d H:i:s');
            }
            $adjustmentAmount = $financialEvent->getAdjustmentAmount();
            $adjustment_currency = '';
            $adjustment_amount = 0.00;
            if (! is_null($adjustmentAmount)) {
                $adjustment_currency = $adjustmentAmount->getCurrencyCode() ?? '';
                $adjustment_amount = $adjustmentAmount->getCurrencyAmount() ?? 0.00;
            }
            $adjustmentItemList = $financialEvent->getAdjustmentItemList();
            $adjustment_item_list = [];
            if (! is_null($adjustmentItemList)) {
                foreach ($adjustmentItemList as $adjustmentItem) {
                    $quantity = $adjustmentItem->getQuantity() ?? '';
                    $perUnitAmount = $adjustmentItem->getPerUnitAmount();
                    $per_unit_currency = '';
                    $per_unit_amount = 0.00;
                    if (! is_null($perUnitAmount)) {
                        $per_unit_currency = $perUnitAmount->getCurrencyCode() ?? '';
                        $per_unit_amount = $perUnitAmount->getCurrencyAmount() ?? 0.00;
                    }
                    $totalAmount = $adjustmentItem->getTotalAmount();
                    $total_amount_currency = '';
                    $total_amount = 0.00;
                    if (! is_null($totalAmount)) {
                        $total_amount_currency = $totalAmount->getCurrencyCode() ?? '';
                        $total_amount = $totalAmount->getCurrencyAmount() ?? 0.00;
                    }
                    $seller_sku = $adjustmentItem->getSellerSku() ?? '';
                    $fn_sku = $adjustmentItem->getFnSku() ?? '';
                    $product_description = $adjustmentItem->getProductDescription() ?? '';
                    $asin = $adjustmentItem->getAsin() ?? '';

                    $adjustment_item_list[] = [
                        'quantity' => $quantity,
                        'per_unit_currency' => $per_unit_currency,
                        'per_unit_amount' => $per_unit_amount,
                        'total_amount_currency' => $total_amount_currency,
                        'total_amount' => $total_amount,
                        'seller_sku' => $seller_sku,
                        'fn_sku' => $fn_sku,
                        'product_description' => $product_description,
                        'asin' => $asin,
                    ];
                }
            }

            $collection->push([
                'merchant_id' => $this->merchant_id,
                'merchant_store_id' => $this->merchant_store_id,
                'adjustment_type' => $adjustment_type,
                'posted_date' => $posted_date,
                'adjustment_currency' => $adjustment_currency,
                'adjustment_amount' => $adjustment_amount,
                'adjustment_item_list' => $adjustment_item_list,
            ]);
        }
        return true;
    }
}
