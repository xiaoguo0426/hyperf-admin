<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Finance;

use AmazonPHP\SellingPartner\Model\Finances\SAFETReimbursementEvent;
use Hyperf\Collection\Collection;

class SAFETReimbursementEventList extends FinanceBase
{
    public function run($financialEvents): bool
    {
        $collection = new Collection();
        /**
         * @var SAFETReimbursementEvent $financialEvent
         */
        foreach ($financialEvents as $financialEvent) {
            $postedDate = $financialEvent->getPostedDate();
            $posted_date = '';
            if (! is_null($postedDate)) {
                $posted_date = $postedDate->format('Y-m-d H:i:s');
            }
            $safet_claim_id = $financialEvent->getSafetClaimId() ?? '';
            $reimbursedAmount = $financialEvent->getReimbursedAmount();
            $reimbursed_amount_currency = '';
            $reimbursed_amount = 0.00;
            if (! is_null($reimbursedAmount)) {
                $reimbursed_amount_currency = $reimbursedAmount->getCurrencyCode() ?? '';
                $reimbursed_amount = $reimbursedAmount->getCurrencyAmount() ?? 0.00;
            }
            $reason_code = $financialEvent->getReasonCode() ?? '';
            $safetReimbursementItemList = $financialEvent->getSafetReimbursementItemList();
            $safet_reimbursement_item_list = [];
            if (! is_null($safetReimbursementItemList)) {
                foreach ($safetReimbursementItemList as $safetReimbursementItem) {
                    $itemChargeList = $safetReimbursementItem->getItemChargeList();
                    $item_charge_list = [];
                    if (! is_null($itemChargeList)) {
                        foreach ($itemChargeList as $itemCharge) {
                            $type = $itemCharge->getChargeType() ?? '';
                            $chargeAmount = $itemCharge->getChargeAmount();
                            $currency = '';
                            $amount = 0.00;
                            if (! is_null($chargeAmount)) {
                                $currency = $chargeAmount->getCurrencyCode();
                                $amount = $chargeAmount->getCurrencyAmount();
                            }
                            $item_charge_list[] = [
                                'type' => $type,
                                'currency' => $currency,
                                'amount' => $amount,
                            ];
                        }
                    }
                    $product_description = $safetReimbursementItem->getProductDescription() ?? '';
                    $quantity = $safetReimbursementItem->getQuantity() ?? '';

                    $safet_reimbursement_item_list[] = [
                        'item_charge_list' => $item_charge_list,
                        'product_description' => $product_description,
                        'quantity' => $quantity,
                    ];
                }
            }

            $collection->push([
                'merchant_id' => $this->merchant_id,
                'merchant_store_id' => $this->merchant_store_id,
                'posted_date' => $posted_date,
                'safet_claim_id' => $safet_claim_id,
                'reimbursed_amount_currency' => $reimbursed_amount_currency,
                'reimbursed_amount' => $reimbursed_amount,
                'reason_code' => $reason_code,
                'safet_reimbursement_item_list' => $safet_reimbursement_item_list,
            ]);
        }
        return true;
    }
}
