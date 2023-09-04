<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Finance;

use AmazonPHP\SellingPartner\Model\Finances\SellerReviewEnrollmentPaymentEvent;
use Hyperf\Collection\Collection;

class SellerReviewEnrollmentPaymentEventList extends FinanceBase
{
    public function run($financialEvents): bool
    {
        $collection = new Collection();
        /**
         * @var SellerReviewEnrollmentPaymentEvent $financialEvent
         */
        foreach ($financialEvents as $financialEvent) {
            $postedDate = $financialEvent->getPostedDate();
            $posted_date = '';
            if (! is_null($postedDate)) {
                $posted_date = $postedDate->format('Y-m-d H:i:s');
            }

            $enrollment_id = $financialEvent->getEnrollmentId() ?? '';
            $parent_asin = $financialEvent->getParentAsin() ?? '';
            $feeComponent = $financialEvent->getFeeComponent();
            $fee_component_type = '';
            $fee_component_currency = '';
            $fee_component_amount = 0.00;
            if (! is_null($feeComponent)) {
                $fee_component_type = $feeComponent->getFeeType() ?? '';
                $feeAmount = $feeComponent->getFeeAmount();
                if (! is_null($feeAmount)) {
                    $fee_component_currency = $feeAmount->getCurrencyCode() ?? '';
                    $fee_component_amount = $feeAmount->getCurrencyAmount() ?? 0.00;
                }
            }
            $chargeComponent = $financialEvent->getChargeComponent();
            $charge_component_type = '';
            $charge_currency = '';
            $charge_amount = 0.00;
            if (! is_null($chargeComponent)) {
                $charge_component_type = $chargeComponent->getChargeType() ?? '';
                $chargeAmount = $chargeComponent->getChargeAmount();
                if (! is_null($chargeAmount)) {
                    $charge_currency = $chargeAmount->getCurrencyCode() ?? '';
                    $charge_amount = $chargeAmount->getCurrencyAmount() ?? 0.00;
                }
            }
            $totalAmount = $financialEvent->getTotalAmount();
            $total_amount_currency = '';
            $total_amount = 0.00;
            if (! is_null($totalAmount)) {
                $total_amount_currency = $totalAmount->getCurrencyCode();
                $total_amount = $totalAmount->getCurrencyAmount();
            }

            $collection->push([
                'merchant_id' => $this->merchant_id,
                'merchant_store_id' => $this->merchant_store_id,
                'posted_date' => $posted_date,
                'enrollment_id' => $enrollment_id,
                'parent_asin' => $parent_asin,
                'fee_component_type' => $fee_component_type,
                'fee_component_currency' => $fee_component_currency,
                'fee_component_amount' => $fee_component_amount,
                'charge_component_type' => $charge_component_type,
                'charge_currency' => $charge_currency,
                'charge_amount' => $charge_amount,
                'total_amount_currency' => $total_amount_currency,
                'total_amount' => $total_amount,
            ]);
        }
        return true;
    }
}
