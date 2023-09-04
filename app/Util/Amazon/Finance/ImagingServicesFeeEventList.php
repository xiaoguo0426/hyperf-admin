<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Finance;

use AmazonPHP\SellingPartner\Model\Finances\ImagingServicesFeeEvent;
use Hyperf\Collection\Collection;

class ImagingServicesFeeEventList extends FinanceBase
{
    public function run($financialEvents): bool
    {
        $collection = new Collection();
        /**
         * @var ImagingServicesFeeEvent $financialEvent
         */
        foreach ($financialEvents as $financialEvent) {
            $postedDate = $financialEvent->getPostedDate();
            $posted_date = '';
            if (! is_null($postedDate)) {
                $posted_date = $postedDate->format('Y-m-d H:i:s');
            }
            $imaging_request_billing_item_id = $financialEvent->getImagingRequestBillingItemId() ?? '';
            $asin = $financialEvent->getAsin() ?? '';
            $feeList = $financialEvent->getFeeList();
            $fee_list = [];
            if (! is_null($feeList)) {
                foreach ($feeList as $fee) {
                    $fee_item = [];
                    $fee_item['type'] = $fee->getFeeType() ?? '';
                    $feeAmount = $fee->getFeeAmount();
                    if (! is_null($feeAmount)) {
                        $fee_item['currency'] = $feeAmount->getCurrencyCode() ?? '';
                        $fee_item['amount'] = $feeAmount->getCurrencyAmount() ?? 0.00;
                    }
                    $fee_list[] = $fee_item;
                }
            }

            $collection->push([
                'merchant_id' => $this->merchant_id,
                'merchant_store_id' => $this->merchant_store_id,
                'imaging_request_billing_item_id' => $imaging_request_billing_item_id,
                'asin' => $asin,
                'posted_date' => $posted_date,
                'fee_list' => $fee_list,
            ]);
        }
        return true;
    }
}
