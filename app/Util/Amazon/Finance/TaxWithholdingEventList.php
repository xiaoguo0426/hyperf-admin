<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Finance;

use AmazonPHP\SellingPartner\Model\Finances\TaxWithholdingEvent;
use Hyperf\Collection\Collection;

class TaxWithholdingEventList extends FinanceBase
{
    public function run($financialEvents): bool
    {
        $collection = new Collection();
        /**
         * @var TaxWithholdingEvent $financialEvent
         */
        foreach ($financialEvents as $financialEvent) {
            $postedDate = $financialEvent->getPostedDate();
            $posted_date = '';
            if (! is_null($postedDate)) {
                $posted_date = $postedDate->format('Y-m-d H:i:s');
            }
            $baseAmount = $financialEvent->getBaseAmount();
            $base_amount_currency = '';
            $base_amount = '';
            if (! is_null($baseAmount)) {
                $base_amount_currency = $baseAmount->getCurrencyCode() ?? '';
                $base_amount = $baseAmount->getCurrencyAmount() ?? 0.00;
            }
            $withheldAmount = $financialEvent->getWithheldAmount();
            $withheld_amount_currency = '';
            $withheld_amount = '';
            if (! is_null($withheldAmount)) {
                $withheld_amount_currency = $withheldAmount->getCurrencyCode() ?? '';
                $withheld_amount = $withheldAmount->getCurrencyAmount() ?? 0.00;
            }
            $taxWithholdingPeriod = $financialEvent->getTaxWithholdingPeriod();
            $tax_withholding_period_start_date = '';
            $tax_withholding_period_end_date = '';
            if (! is_null($taxWithholdingPeriod)) {
                $startDate = $taxWithholdingPeriod->getStartDate();
                if (! is_null($startDate)) {
                    $tax_withholding_period_start_date = $startDate->format('Y-m-d H:i:s');
                }
                $endDate = $taxWithholdingPeriod->getEndDate();
                if (! is_null($endDate)) {
                    $tax_withholding_period_end_date = $endDate->format('Y-m-d H:i:s');
                }
            }

            $collection->push([
                'merchant_id' => $this->merchant_id,
                'merchant_store_id' => $this->merchant_store_id,
                'posted_date' => $posted_date,
                'base_amount_currency' => $base_amount_currency,
                'base_amount' => $base_amount,
                'withheld_amount_currency' => $withheld_amount_currency,
                'withheld_amount' => $withheld_amount,
                'tax_withholding_period_start_date' => $tax_withholding_period_start_date,
                'tax_withholding_period_end_date' => $tax_withholding_period_end_date,
            ]);
        }
        return true;
    }
}
