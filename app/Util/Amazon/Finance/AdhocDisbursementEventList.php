<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Finance;

use AmazonPHP\SellingPartner\Model\Finances\AdhocDisbursementEvent;
use Hyperf\Collection\Collection;

class AdhocDisbursementEventList extends FinanceBase
{
    public function run($financialEvents): bool
    {
        $collection = new Collection();
        /**
         * @var AdhocDisbursementEvent $financialEvent
         */
        foreach ($financialEvents as $financialEvent) {
            $transaction_type = $financialEvent->getTransactionType();
            $postedDate = $financialEvent->getPostedDate();
            $posted_date = '';
            if (! is_null($postedDate)) {
                $posted_date = $postedDate->format('Y-m-d H:i:s');
            }
            $transaction_id = $financialEvent->getTransactionId();
            $transactionAmount = $financialEvent->getTransactionAmount();
            $transaction_amount_currency = '';
            $transaction_amount = 0.00;
            if (! is_null($transactionAmount)) {
                $transaction_amount_currency = $transactionAmount->getCurrencyCode() ?? '';
                $transaction_amount = $transactionAmount->getCurrencyAmount() ?? 0.00;
            }
            $collection->push([
                'merchant_id' => $this->merchant_id,
                'merchant_store_id' => $this->merchant_store_id,
                'transaction_type' => $transaction_type,
                'posted_date' => $posted_date,
                'transaction_id' => $transaction_id,
                'transaction_amount_currency' => $transaction_amount_currency,
                'transaction_amount' => $transaction_amount,
            ]);
        }
        return true;
    }
}
