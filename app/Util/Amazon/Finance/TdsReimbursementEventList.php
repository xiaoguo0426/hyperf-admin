<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Finance;

use AmazonPHP\SellingPartner\Model\Finances\TDSReimbursementEvent;
use Hyperf\Collection\Collection;

class TdsReimbursementEventList extends FinanceBase
{
    public function run($financialEvents): bool
    {
        $collection = new Collection();
        /**
         * @var TDSReimbursementEvent $financialEvent
         */
        foreach ($financialEvents as $financialEvent) {
            $postedDate = $financialEvent->getPostedDate();
            $posted_date = '';
            if (! is_null($postedDate)) {
                $posted_date = $postedDate->format('Y-m-d H:i:s');
            }
            $tds_order_id = $financialEvent->getTdsOrderId();
            $reimbursedAmount = $financialEvent->getReimbursedAmount();
            $reimburse_amount_currency = '';
            $reimburse_amount = 0.00;
            if (! is_null($reimbursedAmount)) {
                $reimburse_amount_currency = $reimbursedAmount->getCurrencyCode() ?? '';
                $reimburse_amount = $reimbursedAmount->getCurrencyAmount() ?? 0.00;
            }

            $collection->push([
                'merchant_id' => $this->merchant_id,
                'merchant_store_id' => $this->merchant_store_id,
                'posted_date' => $posted_date,
                'tds_order_id' => $tds_order_id,
                'reimburse_amount_currency' => $reimburse_amount_currency,
                'reimburse_amount' => $reimburse_amount,
            ]);
        }
        return true;
    }
}
