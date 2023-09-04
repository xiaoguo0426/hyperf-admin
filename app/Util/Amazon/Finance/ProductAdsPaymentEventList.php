<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Finance;

use AmazonPHP\SellingPartner\Model\Finances\ProductAdsPaymentEvent;
use Hyperf\Collection\Collection;

class ProductAdsPaymentEventList extends FinanceBase
{
    public function run($financialEvents): bool
    {
        $collection = new Collection();
        /**
         * @var ProductAdsPaymentEvent $financialEvent
         */
        foreach ($financialEvents as $financialEvent) {
            $postedDate = $financialEvent->getPostedDate();
            $posted_date = '';
            if (! is_null($postedDate)) {
                $posted_date = $postedDate->format('Y-m-d H:i:s');
            }

            $transaction_type = $financialEvent->getTransactionType() ?? '';
            $invoice_id = $financialEvent->getInvoiceId() ?? '';
            $baseValue = $financialEvent->getBaseValue();
            $base_value_currency = '';
            $base_value_amount = 0.00;
            if (! is_null($baseValue)) {
                $base_value_currency = $baseValue->getCurrencyCode() ?? '';
                $base_value_amount = $baseValue->getCurrencyAmount() ?? 0.00;
            }
            $taxValue = $financialEvent->getTaxValue();
            $tax_value_currency = '';
            $tax_value_amount = 0.00;
            if (! is_null($taxValue)) {
                $tax_value_currency = $taxValue->getCurrencyCode() ?? '';
                $tax_value_amount = $taxValue->getCurrencyAmount() ?? 0.00;
            }
            $transactionValue = $financialEvent->getTransactionValue();
            $transaction_value_currency = '';
            $transaction_value_amount = 0.00;
            if (! is_null($transactionValue)) {
                $transaction_value_currency = $transactionValue->getCurrencyCode() ?? '';
                $transaction_value_amount = $transactionValue->getCurrencyAmount() ?? 0.00;
            }

            $collection->push([
                'merchant_id' => $this->merchant_id,
                'merchant_store_id' => $this->merchant_store_id,
                'posted_date' => $posted_date,
                'transaction_type' => $transaction_type,
                'invoice_id' => $invoice_id,
                'base_value_currency' => $base_value_currency,
                'base_value_amount' => $base_value_amount,
                'tax_value_currency' => $tax_value_currency,
                'tax_value_amount' => $tax_value_amount,
                'transaction_value_currency' => $transaction_value_currency,
                'transaction_value_amount' => $transaction_value_amount,
            ]);
        }
        return true;
    }
}
