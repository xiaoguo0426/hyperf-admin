<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Finance;

use AmazonPHP\SellingPartner\Model\Finances\NetworkComminglingTransactionEvent;
use Hyperf\Collection\Collection;

class NetworkComminglingTransactionEventList extends FinanceBase
{
    public function run($financialEvents): bool
    {
        $collection = new Collection();
        /**
         * @var NetworkComminglingTransactionEvent $financialEvent
         */
        foreach ($financialEvents as $financialEvent) {
            $transaction_type = $financialEvent->getTransactionType();
            $postedDate = $financialEvent->getPostedDate();
            $posted_date = '';
            if (! is_null($postedDate)) {
                $posted_date = $postedDate->format('Y-m-d H:i:s');
            }
            $net_co_transaction_id = $financialEvent->getNetCoTransactionId() ?? '';
            $swap_reason = $financialEvent->getSwapReason() ?? '';
            $asin = $financialEvent->getAsin() ?? '';
            $marketplace_id = $financialEvent->getMarketplaceId() ?? '';
            $taxExclusiveAmount = $financialEvent->getTaxExclusiveAmount();
            $tax_exclusive_currency = '';
            $tax_exclusive_amount = 0.00;
            if (! is_null($taxExclusiveAmount)) {
                $tax_exclusive_currency = $taxExclusiveAmount->getCurrencyCode() ?? '';
                $tax_exclusive_amount = $taxExclusiveAmount->getCurrencyAmount() ?? 0.00;
            }
            $taxAmount = $financialEvent->getTaxAmount();
            $tax_currency = '';
            $tax_amount = 0.00;
            if (! is_null($taxAmount)) {
                $tax_currency = $taxAmount->getCurrencyCode() ?? '';
                $tax_amount = $taxAmount->getCurrencyAmount() ?? 0.00;
            }

            $collection->push([
                'merchant_id' => $this->merchant_id,
                'merchant_store_id' => $this->merchant_store_id,
                'transaction_type' => $transaction_type,
                'posted_date' => $posted_date,
                'net_co_transaction_id' => $net_co_transaction_id,
                'swap_reason' => $swap_reason,
                'asin' => $asin,
                'marketplace_id' => $marketplace_id,
                'tax_exclusive_currency' => $tax_exclusive_currency,
                'tax_exclusive_amount' => $tax_exclusive_amount,
                'tax_currency' => $tax_currency,
                'tax_amount' => $tax_amount,
            ]);
        }
        return true;
    }
}
