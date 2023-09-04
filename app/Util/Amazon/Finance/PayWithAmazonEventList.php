<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Finance;

use AmazonPHP\SellingPartner\Model\Finances\PayWithAmazonEvent;
use Hyperf\Collection\Collection;

class PayWithAmazonEventList extends FinanceBase
{
    public function run($financialEvents): bool
    {
        $collection = new Collection();
        /**
         * @var PayWithAmazonEvent $financialEvent
         */
        foreach ($financialEvents as $financialEvent) {
            $seller_order_id = $financialEvent->getSellerOrderId() ?? '';
            $transactionPostedDate = $financialEvent->getTransactionPostedDate();
            $transaction_posted_date = '';
            if (! is_null($transactionPostedDate)) {
                $transaction_posted_date = $transactionPostedDate->format('Y-m-d H:i:s');
            }
            $business_object_type = $financialEvent->getBusinessObjectType() ?? '';
            $sales_channel = $financialEvent->getSalesChannel() ?? '';
            $chargeObject = $financialEvent->getCharge();
            $charge = [];
            if (! is_null($chargeObject)) {
                $charge['type'] = $chargeObject->getChargeType() ?? '';
                $chargeAmount = $chargeObject->getChargeAmount();
                if (! is_null($chargeAmount)) {
                    $charge['currency'] = $chargeAmount->getCurrencyCode() ?? '';
                    $charge['amount'] = $chargeAmount->getCurrencyAmount() ?? 0.00;
                }
            }
            $feeList = $financialEvent->getFeeList();
            $fee_list = [];
            if (! is_null($feeList)) {
                foreach ($feeList as $fee) {
                    $fee_item = [];
                    $fee_item['type'] = $fee->getFeeType() ?? '';
                    $feeAmount = $fee->getFeeAmount();
                    if (! is_null($feeAmount)) {
                        $fee_item['currency'] = $feeAmount->getCurrencyCode() ?? '';
                        $fee_item['amount'] = $feeAmount->getCurrencyAmount() ?? 0.00;
                    }
                    $fee_list[] = $fee_item;
                }
            }
            $payment_amount_type = $financialEvent->getPaymentAmountType() ?? '';
            $amount_description = $financialEvent->getAmountDescription() ?? '';
            $fulfillment_channel = $financialEvent->getFulfillmentChannel() ?? '';
            $store_name = $financialEvent->getStoreName() ?? '';

            $collection->push([
                'merchant_id' => $this->merchant_id,
                'merchant_store_id' => $this->merchant_store_id,
                'seller_order_id' => $seller_order_id,
                'transaction_posted_date' => $transaction_posted_date,
                'business_object_type' => $business_object_type,
                'sales_channel' => $sales_channel,
                'charge' => $charge,
                'fee_list' => $fee_list,
                'payment_amount_type' => $payment_amount_type,
                'amount_description' => $amount_description,
                'fulfillment_channel' => $fulfillment_channel,
                'store_name' => $store_name,
            ]);
        }

        return true;
    }
}
