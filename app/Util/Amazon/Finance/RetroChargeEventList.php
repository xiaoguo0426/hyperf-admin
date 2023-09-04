<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Finance;

use AmazonPHP\SellingPartner\Model\Finances\RetrochargeEvent;
use Hyperf\Collection\Collection;

class RetroChargeEventList extends FinanceBase
{
    public function run($financialEvents): bool
    {
        $collection = new Collection();
        /**
         * @var RetrochargeEvent $financialEvent
         */
        foreach ($financialEvents as $financialEvent) {
            $retro_charge_event_type = $financialEvent->getRetrochargeEventType() ?? '';
            $amazon_order_id = $financialEvent->getAmazonOrderId() ?? '';
            $postedDate = $financialEvent->getPostedDate();
            $posted_date = '';
            if (! is_null($postedDate)) {
                $posted_date = $postedDate->format('Y-m-d H:i:s');
            }
            $baseTax = $financialEvent->getBaseTax();
            $base_tax_currency = '';
            $base_tax_amount = 0.00;
            if (! is_null($baseTax)) {
                $base_tax_currency = $baseTax->getCurrencyCode() ?? '';
                $base_tax_amount = $baseTax->getCurrencyAmount() ?? 0.00;
            }
            $shippingTax = $financialEvent->getShippingTax();
            $shipping_tax_currency = '';
            $shipping_tax_amount = 0.00;
            if (! is_null($shippingTax)) {
                $shipping_tax_currency = $shippingTax->getCurrencyCode() ?? '';
                $shipping_tax_amount = $shippingTax->getCurrencyAmount() ?? 0.00;
            }
            $marketplace_name = $financialEvent->getMarketplaceName() ?? '';
            $retroChargeTaxWithheldList = $financialEvent->getRetrochargeTaxWithheldList();
            $retro_charge_tax_withheld_list = [];
            if (! is_null($retroChargeTaxWithheldList)) {
                foreach ($retroChargeTaxWithheldList as $retroChargeTaxWithheldComponent) {
                    $tax_collection_model = $retroChargeTaxWithheldComponent->getTaxCollectionModel() ?? '';
                    $chargeComponentList = $retroChargeTaxWithheldComponent->getTaxesWithheld();
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
                    $retro_charge_tax_withheld_list[] = [
                        'tax_collection_model' => $tax_collection_model,
                        'taxes_withheld' => $taxes_withheld,
                    ];
                }
            }

            $collection->push([
                'merchant_id' => $this->merchant_id,
                'merchant_store_id' => $this->merchant_store_id,
                'retro_charge_event_type' => $retro_charge_event_type,
                'amazon_order_id' => $amazon_order_id,
                'posted_date' => $posted_date,
                'base_tax_currency' => $base_tax_currency,
                'base_tax_amount' => $base_tax_amount,
                'shipping_tax_currency' => $shipping_tax_currency,
                'shipping_tax_amount' => $shipping_tax_amount,
                'marketplace_name' => $marketplace_name,
                'retro_charge_tax_withheld_list' => $retro_charge_tax_withheld_list,
            ]);
        }
        return true;
    }
}
