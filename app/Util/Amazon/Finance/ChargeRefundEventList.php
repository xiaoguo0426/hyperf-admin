<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Finance;

use AmazonPHP\SellingPartner\Model\Finances\ChargeRefundEvent;
use Hyperf\Collection\Collection;

class ChargeRefundEventList extends FinanceBase
{
    public function run($financialEvents): bool
    {
        $collection = new Collection();
        /**
         * @var ChargeRefundEvent $financialEvent
         */
        foreach ($financialEvents as $financialEvent) {
            $postedDate = $financialEvent->getPostedDate();
            $posted_date = '';
            if (! is_null($postedDate)) {
                $posted_date = $postedDate->format('Y-m-d H:i:s');
            }
            $reason_code = $financialEvent->getReasonCode() ?? '';
            $reason_description = $financialEvent->getReasonCodeDescription() ?? '';
            $chargeRefundTransactions = $financialEvent->getChargeRefundTransactions();
            $charge_refund_transactions_type = '';
            $charge_refund_transactions_currency = '';
            $charge_refund_transactions_amount = 0.00;
            if (! is_null($chargeRefundTransactions)) {
                $charge_refund_transactions_type = $chargeRefundTransactions->getChargeType() ?? '';
                $chargeAmount = $chargeRefundTransactions->getChargeAmount();
                if (! is_null($chargeAmount)) {
                    $charge_refund_transactions_currency = $chargeAmount->getCurrencyCode() ?? '';
                    $charge_refund_transactions_amount = $chargeAmount->getCurrencyAmount() ?? 0.00;
                }
            }

            $collection->push([
                'merchant_id' => $this->merchant_id,
                'merchant_store_id' => $this->merchant_store_id,
                'posted_date' => $posted_date,
                'reason_code' => $reason_code,
                'reason_description' => $reason_description,
                'charge_refund_transactions_type' => $charge_refund_transactions_type,
                'charge_refund_transactions_currency' => $charge_refund_transactions_currency,
                'charge_refund_transactions_amount' => $charge_refund_transactions_amount,
            ]);
        }
        return true;
    }
}
