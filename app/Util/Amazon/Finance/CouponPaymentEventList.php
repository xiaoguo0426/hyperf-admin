<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Finance;

use AmazonPHP\SellingPartner\Model\Finances\CouponPaymentEvent;
use Hyperf\Collection\Collection;

class CouponPaymentEventList extends FinanceBase
{
    public function run($financialEvents): bool
    {
        $collection = new Collection();
        /**
         * @var CouponPaymentEvent $financialEvent
         */
        foreach ($financialEvents as $financialEvent) {
            $postedDate = $financialEvent->getPostedDate();
            $posted_date = '';
            if (! is_null($postedDate)) {
                $posted_date = $postedDate->format('Y-m-d H:i:s');
            }
            $coupon_id = $financialEvent->getCouponId() ?? '';
            $seller_coupon_description = $financialEvent->getSellerCouponDescription() ?? '';
            $clip_or_redemption_count = $financialEvent->getClipOrRedemptionCount() ?? 0;
            $payment_event_id = $financialEvent->getPaymentEventId() ?? '';
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
            $charge_component_currency = '';
            $charge_component_amount = 0.00;
            if (! is_null($chargeComponent)) {
                $charge_component_type = $chargeComponent->getChargeType() ?? '';
                $chargeAmount = $chargeComponent->getChargeAmount();
                if (! is_null($chargeAmount)) {
                    $charge_component_currency = $chargeAmount->getCurrencyCode() ?? '';
                    $charge_component_amount = $chargeAmount->getCurrencyAmount() ?? 0.00;
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
                'coupon_id' => $coupon_id,
                'seller_coupon_description' => $seller_coupon_description,
                'clip_or_redemption_count' => $clip_or_redemption_count,
                'payment_event_id' => $payment_event_id,
                'fee_component_type' => $fee_component_type,
                'fee_component_currency' => $fee_component_currency,
                'fee_component_amount' => $fee_component_amount,
                'charge_component_type' => $charge_component_type,
                'charge_component_currency' => $charge_component_currency,
                'charge_component_amount' => $charge_component_amount,
                'total_amount_currency' => $total_amount_currency,
                'total_amount' => $total_amount,
            ]);
        }
        return true;
    }
}
