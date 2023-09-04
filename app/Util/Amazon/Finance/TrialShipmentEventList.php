<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Finance;

use AmazonPHP\SellingPartner\Model\Finances\TrialShipmentEvent;
use Hyperf\Collection\Collection;

class TrialShipmentEventList extends FinanceBase
{
    public function run($financialEvents): bool
    {
        $collection = new Collection();
        /**
         * @var TrialShipmentEvent $financialEvent
         */
        foreach ($financialEvents as $financialEvent) {
            $amazon_order_id = $financialEvent->getAmazonOrderId();
            $financial_event_group_id = $financialEvent->getFinancialEventGroupId();
            $postedDate = $financialEvent->getPostedDate();
            $posted_date = '';
            if (! is_null($postedDate)) {
                $posted_date = $postedDate->format('Y-m-d H:i:s');
            }
            $sku = $financialEvent->getSku();
            $feeList = $financialEvent->getFeeList();
            $fee_list = [];
            if (! is_null($feeList)) {
                foreach ($feeList as $feeItem) {
                    $type = $feeItem->getFeeType() ?? '';
                    $feeAmount = $feeItem->getFeeAmount();
                    $currency = '';
                    $amount = 0.00;
                    if (! is_null($feeAmount)) {
                        $currency = $feeAmount->getCurrencyCode() ?? '';
                        $amount = $feeAmount->getCurrencyAmount() ?? 0.00;
                    }
                    $fee_list[] = [
                        'type' => $type,
                        'currency' => $currency,
                        'amount' => $amount,
                    ];
                }
            }

            $collection->push([
                'merchant_id' => $this->merchant_id,
                'merchant_store_id' => $this->merchant_store_id,
                'amazon_order_id' => $amazon_order_id,
                'financial_event_group_id' => $financial_event_group_id,
                'posted_date' => $posted_date,
                'sku' => $sku,
                'fee_list' => $fee_list,
            ]);
        }
        return true;
    }
}
