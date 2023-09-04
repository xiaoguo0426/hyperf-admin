<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Finance;

use AmazonPHP\SellingPartner\Model\Finances\ServiceFeeEvent;
use Hyperf\Collection\Collection;

class ServiceFeeEventList extends FinanceBase
{
    public function run($financialEvents): bool
    {
        $collection = new Collection();
        /**
         * @var ServiceFeeEvent $financialEvent
         */
        foreach ($financialEvents as $financialEvent) {
            $amazon_order_id = $financialEvent->getAmazonOrderId() ?? '';
            $fee_reason = $financialEvent->getFeeReason() ?? '';
            $feeList = $financialEvent->getFeeList();
            $fee_list = [];
            if (! is_null($feeList)) {
                foreach ($feeList as $feeItem) {
                    $fee = [];
                    $fee['type'] = $feeItem->getFeeType() ?? '';
                    $feeAmount = $feeItem->getFeeAmount();
                    $fee_currency = '';
                    $fee_amount = 0.00;
                    if (! is_null($feeAmount)) {
                        $fee_currency = $feeAmount->getCurrencyCode() ?? '';
                        $fee_amount = $feeAmount->getCurrencyAmount() ?? 0.00;
                    }
                    $fee['currency'] = $fee_currency;
                    $fee['amount'] = $fee_amount;

                    $fee_list[] = $fee;
                }
            }
            $seller_sku = $financialEvent->getSellerSku() ?? '';
            $fn_sku = $financialEvent->getFnSku() ?? '';
            $fee_description = $financialEvent->getFeeDescription() ?? '';
            $asin = $financialEvent->getAsin() ?? '';

            $collection->push([
                'merchant_id' => $this->merchant_id,
                'merchant_store_id' => $this->merchant_store_id,
                'amazon_order_id' => $amazon_order_id,
                'fee_reason' => $fee_reason,
                'fee_list' => $fee_list,
                'seller_sku' => $seller_sku,
                'fn_sku' => $fn_sku,
                'fee_description' => $fee_description,
                'asin' => $asin,
            ]);
        }
        return true;
    }
}
