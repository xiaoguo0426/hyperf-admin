<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Finance;

use AmazonPHP\SellingPartner\Model\Finances\RentalTransactionEvent;
use Hyperf\Collection\Collection;

class RentalTransactionEventList extends FinanceBase
{
    public function run($financialEvents): bool
    {
        // https://developer-docs.amazon.com/sp-api/docs/finances-api-reference#rentaltransactionevent
        $collection = new Collection();
        /**
         * @var RentalTransactionEvent $financialEvent
         */
        foreach ($financialEvents as $financialEvent) {
            $amazon_order_id = $financialEvent->getAmazonOrderId() ?? '';
            $rental_event_type = $financialEvent->getRentalEventType() ?? '';
            $extension_length = $financialEvent->getExtensionLength() ?? 0;
            $postedDate = $financialEvent->getPostedDate();
            $posted_date = '';
            if (! is_null($postedDate)) {
                $posted_date = $postedDate->format('Y-m-d H:i:s');
            }
            $rentalChargeList = $financialEvent->getRentalChargeList();
            $rental_charge_list = [];
            if (! is_null($rentalChargeList)) {
                foreach ($rentalChargeList as $rentalCharge) {
                    $rental_charge = [];
                    $rental_charge['type'] = $rentalCharge->getChargeType() ?? '';
                    $chargeAmount = $rentalCharge->getChargeAmount();
                    $charge_currency = '';
                    $charge_amount = 0.00;

                    if (! is_null($chargeAmount)) {
                        $charge_currency = $chargeAmount->getCurrencyCode() ?? '';
                        $charge_amount = $chargeAmount->getCurrencyAmount() ?? 0.00;
                    }
                    $rental_charge['currency'] = $charge_currency;
                    $rental_charge['amount'] = $charge_amount;

                    $rental_charge_list[] = $rental_charge;
                }
            }

            $rentalFeeList = $financialEvent->getRentalFeeList();
            $rental_fee_list = [];
            if (! is_null($rentalFeeList)) {
                foreach ($rentalFeeList as $rentalFee) {
                    $rental_fee = [];
                    $rental_fee['type'] = $rentalFee->getFeeType() ?? '';
                    $feeAmount = $rentalFee->getFeeAmount();
                    if (! is_null($feeAmount)) {
                        $rental_fee['currency'] = $feeAmount->getCurrencyCode() ?? '';
                        $rental_fee['amount'] = $feeAmount->getCurrencyAmount() ?? 0.00;
                    }
                    $rental_fee_list[] = $rental_fee;
                }
            }
            $marketplace_name = $financialEvent->getMarketplaceName() ?? '';
            $rentalInitialValue = $financialEvent->getRentalInitialValue();
            $rental_initial_value_currency = '';
            $rental_initial_value_amount = 0.00;
            if (! is_null($rentalInitialValue)) {
                $rental_initial_value_currency = $rentalInitialValue->getCurrencyCode() ?? '';
                $rental_initial_value_amount = $rentalInitialValue->getCurrencyAmount() ?? 0.00;
            }
            $rentalReimbursement = $financialEvent->getRentalReimbursement();
            $rental_reimbursement_currency = '';
            $rental_reimbursement_amount = 0.00;
            if (! is_null($rentalReimbursement)) {
                $rental_reimbursement_currency = $rentalReimbursement->getCurrencyCode() ?? '';
                $rental_reimbursement_amount = $rentalReimbursement->getCurrencyAmount() ?? 0.00;
            }
            $rentalTaxWithheldList = $financialEvent->getRentalTaxWithheldList();
            $rental_tax_withheld_list = [];
            if (! is_null($rentalTaxWithheldList)) {
                foreach ($rentalTaxWithheldList as $rentalTaxWithheldComponent) {
                    $tax_collection_model = $rentalTaxWithheldComponent->getTaxCollectionModel() ?? '';
                    $chargeComponentList = $rentalTaxWithheldComponent->getTaxesWithheld();
                    $taxes_withheld = [];
                    if (! is_null($chargeComponentList)) {
                        foreach ($chargeComponentList as $chargeComponent) {
                            $tax_withheld = [];
                            $tax_withheld['type'] = $chargeComponent->getChargeType() ?? '';
                            $chargeAmount = $chargeComponent->getChargeAmount();
                            $charge_currency = '';
                            $charge_amount = 0.00;
                            if (! is_null($chargeAmount)) {
                                $charge_currency = $chargeAmount->getCurrencyCode() ?? '';
                                $charge_amount = $chargeAmount->getCurrencyAmount() ?? 0.00;
                            }
                            $tax_withheld['currency'] = $charge_currency;
                            $tax_withheld['amount'] = $charge_amount;

                            $taxes_withheld[] = $tax_withheld;
                        }
                    }
                    $rental_tax_withheld_list[] = [
                        'tax_collection_model' => $tax_collection_model,
                        'taxes_withheld' => $taxes_withheld,
                    ];
                }
            }

            $collection->push([
                'merchant_id' => $this->merchant_id,
                'merchant_store_id' => $this->merchant_store_id,
                'amazon_order_id' => $amazon_order_id,
                'rental_event_type' => $rental_event_type,
                'extension_length' => $extension_length,
                'posted_date' => $posted_date,
                'rental_charge_list' => $rental_charge_list,
                'rental_fee_list' => $rental_fee_list,
                'marketplace_name' => $marketplace_name,
                'rental_initial_value_currency' => $rental_initial_value_currency,
                'rental_initial_value_amount' => $rental_initial_value_amount,
                'rental_reimbursement_currency' => $rental_reimbursement_currency,
                'rental_reimbursement_amount' => $rental_reimbursement_amount,
                'rental_tax_withheld_list' => $rental_tax_withheld_list,
            ]);
        }
        return true;
    }
}
