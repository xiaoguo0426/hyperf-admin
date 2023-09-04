<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Finance;

use AmazonPHP\SellingPartner\Model\Finances\ValueAddedServiceChargeEventList as ValueAddedServiceChargeEventListNew;
use Hyperf\Collection\Collection;

class ValueAddedServiceChargeEventList extends FinanceBase
{
    public function run($financialEvents): bool
    {
        $collection = new Collection();
        /**
         * @var ValueAddedServiceChargeEventListNew $financialEvents
         */
        $transaction_type = $financialEvents->getTransactionType();
        $postedDate = $financialEvents->getPostedDate();
        $posted_date = '';
        if (! is_null($postedDate)) {
            $posted_date = $postedDate->format('Y-m-d H:i:s');
        }
        $description = $financialEvents->getDescription();
        $transactionAmount = $financialEvents->getTransactionAmount();
        $transaction_currency = '';
        $transaction_amount = 0.00;
        if (! is_null($transactionAmount)) {
            $transaction_currency = $transactionAmount->getCurrencyCode() ?? '';
            $transaction_amount = $transactionAmount->getCurrencyAmount() ?? 0.00;
        }

        if ($transaction_type === '' && $posted_date === '' && $description === '') {
            return true;
        }

        $collection->push([
            'merchant_id' => $this->merchant_id,
            'merchant_store_id' => $this->merchant_store_id,
            'transaction_type' => $transaction_type,
            'posted_date' => $posted_date,
            'description' => $description,
            'transaction_currency' => $transaction_currency,
            'transaction_amount' => $transaction_amount,
        ]);
        return true;
    }
}
