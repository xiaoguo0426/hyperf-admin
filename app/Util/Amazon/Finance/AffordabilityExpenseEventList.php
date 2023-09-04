<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Finance;

use AmazonPHP\SellingPartner\Model\Finances\AffordabilityExpenseEvent;
use Hyperf\Collection\Collection;

class AffordabilityExpenseEventList extends FinanceBase
{
    public function run($financialEvents): bool
    {
        $collection = new Collection();
        /**
         * @var AffordabilityExpenseEvent $financialEvent
         */
        foreach ($financialEvents as $financialEvent) {
            $amazon_order_id = $financialEvent->getAmazonOrderId();
            $postedDate = $financialEvent->getPostedDate();
            $posted_date = '';
            if (! is_null($postedDate)) {
                $posted_date = $postedDate->format('Y-m-d H:i:s');
            }
            $marketplace_id = $financialEvent->getMarketplaceId() ?? '';
            $transaction_type = $financialEvent->getTransactionType() ?? '';
            $baseExpense = $financialEvent->getBaseExpense();
            $base_expense_currency = '';
            $base_expense_amount = 0.00;
            if (! is_null($baseExpense)) {
                $base_expense_currency = $baseExpense->getCurrencyCode() ?? '';
                $base_expense_amount = $baseExpense->getCurrencyAmount() ?? 0.00;
            }
            $taxTypeCgst = $financialEvent->getTaxTypeCgst();
            $tax_type_cgst_currency = '';
            $tax_type_cgst_amount = 0.00;
            if (! is_null($taxTypeCgst)) {
                $tax_type_cgst_currency = $taxTypeCgst->getCurrencyCode() ?? '';
                $tax_type_cgst_amount = $taxTypeCgst->getCurrencyAmount() ?? 0.00;
            }
            $taxTypeSgst = $financialEvent->getTaxTypeSgst();
            $tax_type_sgst_currency = '';
            $tax_type_sgst_amount = 0.00;
            if (! is_null($taxTypeSgst)) {
                $tax_type_sgst_currency = $taxTypeSgst->getCurrencyCode() ?? '';
                $tax_type_sgst_amount = $taxTypeSgst->getCurrencyAmount() ?? 0.00;
            }
            $taxTypeIgst = $financialEvent->getTaxTypeIgst();
            $tax_type_igst_currency = '';
            $tax_type_igst_amount = 0.00;
            if (! is_null($taxTypeIgst)) {
                $tax_type_igst_currency = $taxTypeIgst->getCurrencyCode() ?? '';
                $tax_type_igst_amount = $taxTypeIgst->getCurrencyAmount() ?? 0.00;
            }
            $totalExpense = $financialEvent->getTotalExpense();
            $total_expense_currency = '';
            $total_expense_amount = 0.00;
            if (! is_null($totalExpense)) {
                $total_expense_currency = $totalExpense->getCurrencyCode() ?? '';
                $total_expense_amount = $totalExpense->getCurrencyAmount() ?? 0.00;
            }

            $collection->push([
                'merchant_id' => $this->merchant_id,
                'merchant_store_id' => $this->merchant_store_id,
                'amazon_order_id' => $amazon_order_id,
                'posted_date' => $posted_date,
                'marketplace_id' => $marketplace_id,
                'transaction_type' => $transaction_type,
                'base_expense_currency' => $base_expense_currency,
                'base_expense_amount' => $base_expense_amount,
                'tax_type_cgst_currency' => $tax_type_cgst_currency,
                'tax_type_cgst_amount' => $tax_type_cgst_amount,
                'tax_type_sgst_currency' => $tax_type_sgst_currency,
                'tax_type_sgst_amount' => $tax_type_sgst_amount,
                'tax_type_igst_currency' => $tax_type_igst_currency,
                'tax_type_igst_amount' => $tax_type_igst_amount,
                'total_expense_currency' => $total_expense_currency,
                'total_expense_amount' => $total_expense_amount,
            ]);
        }
        return true;
    }
}
