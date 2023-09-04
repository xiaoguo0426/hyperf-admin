<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Finance;

use AmazonPHP\SellingPartner\Model\Finances\LoanServicingEvent;
use Hyperf\Collection\Collection;

class LoanServicingEventList extends FinanceBase
{
    public function run($financialEvents): bool
    {
        $collection = new Collection();
        /**
         * @var LoanServicingEvent $financialEvent
         */
        foreach ($financialEvents as $financialEvent) {
            $loanAmount = $financialEvent->getLoanAmount();
            $loan_currency = '';
            $loan_amount = 0.00;
            if (! is_null($loanAmount)) {
                $loan_currency = $loanAmount->getCurrencyCode() ?? '';
                $loan_amount = $loanAmount->getCurrencyAmount() ?? 0.00;
            }
            $source_business_event_type = $financialEvent->getSourceBusinessEventType() ?? '';

            $collection->push([
                'merchant_id' => $this->merchant_id,
                'merchant_store_id' => $this->merchant_store_id,
                'loan_currency' => $loan_currency,
                'loan_amount' => $loan_amount,
                'source_business_event_type' => $source_business_event_type,
            ]);
        }
        return true;
    }
}
