<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Finance;

use AmazonPHP\SellingPartner\Model\Finances\SellerDealPaymentEvent;
use Hyperf\Collection\Collection;

class SellerDealPaymentEventList extends FinanceBase
{
    public function run($financialEvents): bool
    {
        $collection = new Collection();
        /**
         * @var SellerDealPaymentEvent $financialEvent
         */
        foreach ($financialEvents as $financialEvent) {
            $postedDate = $financialEvent->getPostedDate();
            $posted_date = '';
            if (! is_null($postedDate)) {
                $posted_date = $postedDate->format('Y-m-d H:i:s');
            }
            $deal_id = $financialEvent->getDealId() ?? '';
            $deal_description = $financialEvent->getDealDescription() ?? '';
            $event_type = $financialEvent->getEventType() ?? '';
            $fee_type = $financialEvent->getFeeType() ?? '';
            $feeAmount = $financialEvent->getFeeAmount();
            $fee_currency = '';
            $fee_amount = 0.00;
            if (! is_null($feeAmount)) {
                $fee_currency = $feeAmount->getCurrencyCode() ?? '';
                $fee_amount = $feeAmount->getCurrencyAmount() ?? 0.00;
            }
            $taxAmount = $financialEvent->getTaxAmount();
            $tax_currency = '';
            $tax_amount = 0.00;
            if (! is_null($taxAmount)) {
                $tax_currency = $taxAmount->getCurrencyCode() ?? '';
                $tax_amount = $taxAmount->getCurrencyAmount() ?? 0.00;
            }
            $totalAmount = $financialEvent->getTotalAmount();
            $total_currency = '';
            $total_amount = 0.00;
            if (! is_null($totalAmount)) {
                $total_currency = $totalAmount->getCurrencyCode() ?? '';
                $total_amount = $totalAmount->getCurrencyAmount() ?? 0.00;
            }
            $collection->push([
                'merchant_id' => $this->merchant_id,
                'merchant_store_id' => $this->merchant_store_id,
                'posted_date' => $posted_date,
                'deal_id' => $deal_id,
                'deal_description' => $deal_description,
                'event_type' => $event_type,
                'fee_type' => $fee_type,
                'fee_currency' => $fee_currency,
                'fee_amount' => $fee_amount,
                'tax_currency' => $tax_currency,
                'tax_amount' => $tax_amount,
                'total_currency' => $total_currency,
                'total_amount' => $total_amount,
            ]);
        }
        return true;
    }
}
