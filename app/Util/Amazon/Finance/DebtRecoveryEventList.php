<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Finance;

use AmazonPHP\SellingPartner\Model\Finances\DebtRecoveryEvent;
use Hyperf\Collection\Collection;

class DebtRecoveryEventList extends FinanceBase
{
    public function run($financialEvents): bool
    {
        $collection = new Collection();
        /**
         * @var DebtRecoveryEvent $financialEvent
         */
        foreach ($financialEvents as $financialEvent) {
            $debt_recovery_type = $financialEvent->getDebtRecoveryType() ?? '';
            $recoveryAmount = $financialEvent->getRecoveryAmount();
            $recovery_currency = '';
            $recovery_amount = 0.00;
            if (! is_null($recoveryAmount)) {
                $recovery_currency = $recoveryAmount->getCurrencyCode() ?? '';
                $recovery_amount = $recoveryAmount->getCurrencyAmount() ?? 0.00;
            }
            $overPaymentCredit = $financialEvent->getOverPaymentCredit();
            $over_payment_credit_currency = '';
            $over_payment_credit_amount = 0.00;
            if (! is_null($overPaymentCredit)) {
                $over_payment_credit_currency = $overPaymentCredit->getCurrencyCode() ?? '';
                $over_payment_credit_amount = $overPaymentCredit->getCurrencyAmount() ?? 0.00;
            }
            $debtRecoveryItemList = $financialEvent->getDebtRecoveryItemList();
            $debt_recovery_item_list = [];
            if (! is_null($debtRecoveryItemList)) {
                foreach ($debtRecoveryItemList as $debtRecoveryItem) {
                    $recoveryAmount = $debtRecoveryItem->getRecoveryAmount();
                    $recovery_currency = '';
                    $recovery_amount = 0.00;
                    if (! is_null($recoveryAmount)) {
                        $recovery_currency = $recoveryAmount->getCurrencyCode() ?? '';
                        $recovery_amount = $recoveryAmount->getCurrencyAmount() ?? 0.00;
                    }
                    $originalAmount = $debtRecoveryItem->getOriginalAmount();
                    $original_currency = '';
                    $original_amount = 0.00;
                    if (! is_null($originalAmount)) {
                        $original_currency = $originalAmount->getCurrencyCode() ?? '';
                        $original_amount = $originalAmount->getCurrencyAmount() ?? 0.00;
                    }
                    $groupBeginDate = $debtRecoveryItem->getGroupBeginDate();
                    $group_begin_date = '';
                    if (! is_null($groupBeginDate)) {
                        $group_begin_date = $groupBeginDate->format('Y-m-d H:i:s');
                    }
                    $groupEndDate = $debtRecoveryItem->getGroupEndDate();
                    $group_end_date = '';
                    if (! is_null($groupEndDate)) {
                        $group_end_date = $groupEndDate->format('Y-m-d H:i:s');
                    }

                    $debt_recovery_item_list[] = [
                        'recovery_currency' => $recovery_currency,
                        'recovery_amount' => $recovery_amount,
                        'original_currency' => $original_currency,
                        'original_amount' => $original_amount,
                        'group_begin_date' => $group_begin_date,
                        'group_end_date' => $group_end_date,
                    ];
                }
            }
            $chargeInstrumentList = $financialEvent->getChargeInstrumentList();
            $charge_instrument_list = [];
            if (! is_null($chargeInstrumentList)) {
                foreach ($chargeInstrumentList as $chargeInstrument) {
                    $description = $chargeInstrument->getDescription() ?? '';
                    $tail = $chargeInstrument->getTail() ?? '';
                    $instrumentAmount = $chargeInstrument->getAmount();
                    $currency = '';
                    $amount = 0.00;
                    if (! is_null($instrumentAmount)) {
                        $currency = $instrumentAmount->getCurrencyCode() ?? '';
                        $amount = $instrumentAmount->getCurrencyAmount() ?? 0.00;
                    }
                    $charge_instrument_list[] = [
                        'description' => $description,
                        'tail' => $tail,
                        'currency' => $currency,
                        'amount' => $amount,
                    ];
                }
            }

            $collection->push([
                'merchant_id' => $this->merchant_id,
                'merchant_store_id' => $this->merchant_store_id,
                'debt_recovery_type' => $debt_recovery_type,
                'recovery_currency' => $recovery_currency,
                'recovery_amount' => $recovery_amount,
                'over_payment_credit_currency' => $over_payment_credit_currency,
                'over_payment_credit_amount' => $over_payment_credit_amount,
                'debt_recovery_item_list' => $debt_recovery_item_list,
                'charge_instrument_list' => $charge_instrument_list,
            ]);
        }
        return true;
    }
}
