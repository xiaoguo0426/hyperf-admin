<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Finance;

use AmazonPHP\SellingPartner\Model\Finances\ShipmentEvent;
use Hyperf\Collection\Collection;

class ShipmentSettleEventList extends FinanceBase
{
    public function run($financialEvents): bool
    {
        $collection = new Collection();
        /**
         * @var ShipmentEvent $financialEvent
         */
        foreach ($financialEvents as $financialEvent) {
            $amazon_order_id = $financialEvent->getAmazonOrderId() ?? '';
            $seller_order_id = $financialEvent->getSellerOrderId() ?? '';
            $marketplace_name = $financialEvent->getMarketplaceName() ?? '';

            $orderChargeList = $financialEvent->getOrderChargeList();
            $order_charge_list = [];
            if (! is_null($orderChargeList)) {
                foreach ($orderChargeList as $orderChargeItem) {
                    $charge_type = $orderChargeItem->getChargeType() ?? ''; // 卖方账户上的费用类型

                    $chargeAmount = $orderChargeItem->getChargeAmount();
                    $charge_amount = 0.00;
                    $charge_amount_currency = '';
                    if (! is_null($chargeAmount)) {
                        $charge_amount = $chargeAmount->getCurrencyAmount() ?? 0.00;
                        $charge_amount_currency = $chargeAmount->getCurrencyCode() ?? '';
                    }

                    $order_charge_list[] = [
                        'charge_type' => $charge_type,
                        'charge_amount' => $charge_amount,
                        'charge_amount_currency' => $charge_amount_currency,
                    ];
                }
            }

            $orderChargeAdjustmentList = $financialEvent->getOrderChargeAdjustmentList();
            $order_charge_adjustment_list = [];
            if (! is_null($orderChargeAdjustmentList)) {
                foreach ($orderChargeAdjustmentList as $orderChargeAdjustmentItem) {
                    $charge_type = $orderChargeAdjustmentItem->getChargeType() ?? ''; // 卖方账户上的费用类型

                    $chargeAmount = $orderChargeAdjustmentItem->getChargeAmount();
                    $charge_amount = 0.00;
                    $charge_amount_currency = '';
                    if (! is_null($chargeAmount)) {
                        $charge_amount = $chargeAmount->getCurrencyAmount() ?? 0.00;
                        $charge_amount_currency = $chargeAmount->getCurrencyCode() ?? '';
                    }

                    $order_charge_adjustment_list[] = [
                        'charge_type' => $charge_type,
                        'charge_amount' => $charge_amount,
                        'charge_amount_currency' => $charge_amount_currency,
                    ];
                }
            }

            $shipmentFeeList = $financialEvent->getShipmentFeeList();
            $shipment_fee_list = [];
            if (! is_null($shipmentFeeList)) {
                foreach ($shipmentFeeList as $shipmentFeeItem) {
                    $shipmentFeeItem->getFeeType(); // 费用类型
                    $shipmentFeeAmount = $shipmentFeeItem->getFeeAmount(); // 费用金额
                    $shipment_fee_amount = 0.00;
                    $shipment_fee_amount_currency = '';
                    if (! is_null($shipmentFeeAmount)) {
                        $shipment_fee_amount = $shipmentFeeAmount->getCurrencyAmount() ?? 0.00;
                        $shipment_fee_amount_currency = $shipmentFeeAmount->getCurrencyCode() ?? '';
                    }

                    $shipment_fee_list[] = [
                        'fee_type' => $shipmentFeeItem->getFeeType() ?? '',
                        'fee_amount' => $shipment_fee_amount,
                        'fee_amount_currency' => $shipment_fee_amount_currency,
                    ];
                }
            }

            $shipmentFeeAdjustmentList = $financialEvent->getShipmentFeeAdjustmentList();
            $shipment_fee_adjustment_list = [];
            if (! is_null($shipmentFeeAdjustmentList)) {
                foreach ($shipmentFeeAdjustmentList as $shipmentFeeAdjustmentItem) {
                    $shipmentFeeAmount = $shipmentFeeAdjustmentItem->getFeeAmount(); // 费用金额
                    $fee_amount = 0.00;
                    $fee_amount_currency = '';
                    if (! is_null($shipmentFeeAmount)) {
                        $fee_amount = $shipmentFeeAmount->getCurrencyAmount() ?? 0.00;
                        $fee_amount_currency = $shipmentFeeAmount->getCurrencyCode() ?? '';
                    }

                    $shipment_fee_adjustment_list[] = [
                        'fee_type' => $shipmentFeeAdjustmentItem->getFeeType() ?? '',
                        'fee_amount' => $fee_amount,
                        'fee_amount_currency' => $fee_amount_currency,
                    ];
                }
            }

            $shipmentItemAdjustmentList = $financialEvent->getShipmentItemAdjustmentList();
            $shipment_item_adjustment_list = [];
            if (! is_null($shipmentItemAdjustmentList)) {
                foreach ($shipmentItemAdjustmentList as $shipmentItemAdjustmentItem) {
                    $seller_sku = $shipmentItemAdjustmentItem->getSellerSku() ?? '';
                    $order_item_id = $shipmentItemAdjustmentItem->getOrderItemId() ?? '';
                    $order_adjustment_item_id = $shipmentItemAdjustmentItem->getOrderAdjustmentItemId() ?? '';
                    $quantity_shipped = $shipmentItemAdjustmentItem->getQuantityShipped() ?? 0;

                    $itemChargeList = $shipmentItemAdjustmentItem->getItemChargeList();
                    $item_charge_list = [];
                    if (! is_null($itemChargeList)) {
                        foreach ($itemChargeList as $itemChargeItem) {
                            $charge_type = $itemChargeItem->getChargeType() ?? '';
                            $itemChargeAmount = $itemChargeItem->getChargeAmount();
                            $charge_amount = 0.00;
                            $charge_amount_currency = '';
                            if (! is_null($itemChargeAmount)) {
                                $charge_amount = $itemChargeAmount->getCurrencyAmount();
                                $charge_amount_currency = $itemChargeAmount->getCurrencyCode();
                            }
                            $item_charge_list[] = [
                                'charge_type' => $charge_type,
                                'charge_amount' => $charge_amount,
                                'charge_amount_currency' => $charge_amount_currency,
                            ];
                        }
                    }

                    $itemChargeAdjustmentList = $shipmentItemAdjustmentItem->getItemChargeAdjustmentList();
                    $item_charge_adjustment_list = [];
                    if (! is_null($itemChargeAdjustmentList)) {
                        foreach ($itemChargeAdjustmentList as $itemChargeAdjustmentItem) {
                            $charge_type = $itemChargeAdjustmentItem->getChargeType() ?? '';
                            $itemChargeAdjustmentAmount = $itemChargeAdjustmentItem->getChargeAmount();
                            $charge_amount = 0.00;
                            $charge_amount_currency = '';
                            if (! is_null($itemChargeAdjustmentAmount)) {
                                $charge_amount = $itemChargeAdjustmentAmount->getCurrencyAmount();
                                $charge_amount_currency = $itemChargeAdjustmentAmount->getCurrencyCode();
                            }
                            $item_charge_adjustment_list[] = [
                                'charge_type' => $charge_type,
                                'charge_amount' => $charge_amount,
                                'charge_amount_currency' => $charge_amount_currency,
                            ];
                        }
                    }

                    $itemFeeList = $shipmentItemAdjustmentItem->getItemFeeList();
                    $item_fee_list = [];
                    if (! is_null($itemFeeList)) {
                        foreach ($itemFeeList as $itemFeeItem) {
                            $fee_type = $itemFeeItem->getFeeType() ?? '';
                            $feeAmount = $itemFeeItem->getFeeAmount();
                            $fee_amount = 0.00;
                            $fee_currency = '';
                            if (! is_null($feeAmount)) {
                                $fee_amount = $feeAmount->getCurrencyAmount() ?? 0.00;
                                $fee_currency = $feeAmount->getCurrencyCode() ?? '';
                            }
                            $item_fee_list[] = [
                                'fee_type' => $fee_type,
                                'fee_amount' => $fee_amount,
                                'fee_currency' => $fee_currency,
                            ];
                        }
                    }

                    $itemFeeAdjustmentList = $shipmentItemAdjustmentItem->getItemFeeAdjustmentList();
                    $item_fee_adjustment_list = [];
                    if (! is_null($itemFeeAdjustmentList)) {
                        foreach ($itemFeeAdjustmentList as $itemFeeAdjustmentItem) {
                            $fee_type = $itemFeeAdjustmentItem->getFeeType() ?? '';
                            $feeAmount = $itemFeeAdjustmentItem->getFeeAmount();
                            $fee_amount = 0.00;
                            $fee_currency = '';
                            if (! is_null($feeAmount)) {
                                $fee_amount = $feeAmount->getCurrencyAmount() ?? 0.00;
                                $fee_currency = $feeAmount->getCurrencyCode() ?? '';
                            }
                            $item_fee_adjustment_list[] = [
                                'fee_type' => $fee_type,
                                'fee_amount' => $fee_amount,
                                'fee_currency' => $fee_currency,
                            ];
                        }
                    }

                    $itemTaxWithheldList = $shipmentItemAdjustmentItem->getItemTaxWithheldList();
                    $item_tax_withheld_list = [];
                    if (! is_null($itemTaxWithheldList)) {
                        foreach ($itemTaxWithheldList as $itemTaxWithheldItem) {
                            $tax_collection = $itemTaxWithheldItem->getTaxCollectionModel();
                            $taxesWithheld = $itemTaxWithheldItem->getTaxesWithheld();
                            $taxes_with_held_list = [];
                            if (! is_null($taxesWithheld)) {
                                foreach ($taxesWithheld as $taxWithheld) {
                                    $charge_type = $taxWithheld->getChargeType(); // https://developer-docs.amazon.com/sp-api/docs/finances-api-reference#chargecomponent
                                    $chargeAmount = $taxWithheld->getChargeAmount();
                                    $charge_amount = 0.00;
                                    $charge_currency = '';
                                    if (! is_null($chargeAmount)) {
                                        $charge_amount = $chargeAmount->getCurrencyAmount();
                                        $charge_currency = $chargeAmount->getCurrencyCode();
                                    }
                                    $taxes_with_held_list[] = [
                                        'charge_type' => $charge_type,
                                        'charge_amount' => $charge_amount,
                                        'charge_currency' => $charge_currency,
                                    ];
                                }
                            }
                            $item_tax_withheld_list[] = [
                                'tax_collection' => $tax_collection,
                                'taxes_with_held_list' => $taxes_with_held_list,
                            ];
                        }
                    }

                    $promotionList = $shipmentItemAdjustmentItem->getPromotionList();
                    $promotion_list = [];
                    if (! is_null($promotionList)) {
                        foreach ($promotionList as $promotionItem) {
                            $promotion_id = $promotionItem->getPromotionId() ?? '';
                            $promotion_type = $promotionItem->getPromotionType() ?? '';
                            $promotionAmount = $promotionItem->getPromotionAmount();
                            $promotion_amount = 0.00;
                            $promotion_currency = '';
                            if (! is_null($promotionAmount)) {
                                $promotion_amount = $promotionAmount->getCurrencyAmount() ?? 0.00;
                                $promotion_currency = $promotionAmount->getCurrencyCode() ?? '';
                            }
                            $promotion_list[] = [
                                'promotion_id' => $promotion_id,
                                'promotion_type' => $promotion_type,
                                'promotion_amount' => $promotion_amount,
                                'promotion_currency' => $promotion_currency,
                            ];
                        }
                    }

                    $promotionAdjustmentList = $shipmentItemAdjustmentItem->getPromotionAdjustmentList();
                    $promotion_adjustment_list = [];
                    if (! is_null($promotionAdjustmentList)) {
                        foreach ($promotionAdjustmentList as $promotionAdjustmentItem) {
                            $promotion_id = $promotionAdjustmentItem->getPromotionId() ?? '';
                            $promotion_type = $promotionAdjustmentItem->getPromotionType() ?? '';
                            $promotionAmount = $promotionAdjustmentItem->getPromotionAmount();
                            $promotion_amount = 0.00;
                            $promotion_currency = '';
                            if (! is_null($promotionAmount)) {
                                $promotion_amount = $promotionAmount->getCurrencyAmount() ?? 0.00;
                                $promotion_currency = $promotionAmount->getCurrencyCode() ?? '';
                            }
                            $promotion_adjustment_list[] = [
                                'promotion_id' => $promotion_id,
                                'promotion_type' => $promotion_type,
                                'promotion_amount' => $promotion_amount,
                                'promotion_currency' => $promotion_currency,
                            ];
                        }
                    }

                    $costOfPointsGranted = $shipmentItemAdjustmentItem->getCostOfPointsGranted();
                    $cost_of_points_granted_amount = 0.00;
                    $cost_of_points_granted_currency = '';
                    if (! is_null($costOfPointsGranted)) {
                        $cost_of_points_granted_amount = $costOfPointsGranted->getCurrencyAmount() ?? 0.00;
                        $cost_of_points_granted_currency = $costOfPointsGranted->getCurrencyCode() ?? '';
                    }

                    $costOfPointsReturned = $shipmentItemAdjustmentItem->getCostOfPointsReturned();
                    $cost_of_points_returned_amount = 0.00;
                    $cost_of_points_returned_currency = '';
                    if (! is_null($costOfPointsReturned)) {
                        $cost_of_points_returned_amount = $costOfPointsReturned->getCurrencyAmount() ?? 0.00;
                        $cost_of_points_returned_currency = $costOfPointsReturned->getCurrencyCode() ?? '';
                    }

                    $shipment_item_adjustment_list[] = [
                        'seller_sku' => $seller_sku,
                        'order_item_id' => $order_item_id,
                        'order_adjustment_item_id' => $order_adjustment_item_id,
                        'quantity_shipped' => $quantity_shipped,
                        'item_charge_list' => $item_charge_list,
                        'item_charge_adjustment_list' => $item_charge_adjustment_list,
                        'item_fee_list' => $item_fee_list,
                        'item_fee_adjustment_list' => $item_fee_adjustment_list,
                        'item_tax_withheld_list' => $item_tax_withheld_list,
                        'promotion_list' => $promotion_list,
                        'promotion_adjustment_list' => $promotion_adjustment_list,
                        'cost_of_points_granted_amount' => $cost_of_points_granted_amount,
                        'cost_of_points_granted_currency' => $cost_of_points_granted_currency,
                        'cost_of_points_returned_amount' => $cost_of_points_returned_amount,
                        'cost_of_points_returned_currency' => $cost_of_points_returned_currency,
                    ];
                }
            }

            $orderFeeList = $financialEvent->getOrderFeeList();
            $order_fee_list = [];
            if (! is_null($orderFeeList)) {
                foreach ($orderFeeList as $orderFeeItem) {
                    $fee_type = $orderFeeItem->getFeeType() ?? '';
                    $feeAmount = $orderFeeItem->getFeeAmount();
                    $fee_amount = 0.00;
                    $fee_currency = '';
                    if (! is_null($feeAmount)) {
                        $fee_amount = $feeAmount->getCurrencyAmount() ?? 0.00;
                        $fee_currency = $feeAmount->getCurrencyCode() ?? '';
                    }
                    $order_fee_list[] = [
                        'fee_type' => $fee_type,
                        'fee_amount' => $fee_amount,
                        'fee_currency' => $fee_currency,
                    ];
                }
            }

            $orderFeeAdjustmentList = $financialEvent->getOrderFeeAdjustmentList();
            $order_fee_adjustment_list = [];
            if (! is_null($orderFeeAdjustmentList)) {
                foreach ($orderFeeAdjustmentList as $orderFeeAdjustmentItem) {
                    $fee_type = $orderFeeAdjustmentItem->getFeeType() ?? '';
                    $feeAmount = $orderFeeAdjustmentItem->getFeeAmount();
                    $fee_amount = 0.00;
                    $fee_currency = '';
                    if (! is_null($feeAmount)) {
                        $fee_amount = $feeAmount->getCurrencyAmount() ?? 0.00;
                        $fee_currency = $feeAmount->getCurrencyCode() ?? '';
                    }
                    $order_fee_adjustment_list[] = [
                        'fee_type' => $fee_type,
                        'fee_amount' => $fee_amount,
                        'fee_currency' => $fee_currency,
                    ];
                }
            }

            $directPaymentList = $financialEvent->getDirectPaymentList();
            $direct_payment_list = [];
            if (! is_null($directPaymentList)) {
                foreach ($directPaymentList as $directPaymentItem) {
                    $direct_payment_type = $directPaymentItem->getDirectPaymentType() ?? '';
                    $directPaymentAmount = $directPaymentItem->getDirectPaymentAmount();
                    $direct_payment_amount = 0.00;
                    $direct_payment_currency = '';
                    if (! is_null($directPaymentAmount)) {
                        $direct_payment_amount = $directPaymentAmount->getCurrencyAmount() ?? 0.00;
                        $direct_payment_currency = $directPaymentAmount->getCurrencyCode() ?? '';
                    }
                    $direct_payment_list[] = [
                        'direct_payment_type' => $direct_payment_type,
                        'direct_payment_amount' => $direct_payment_amount,
                        'direct_payment_currency' => $direct_payment_currency,
                    ];
                }
            }

            $postedDate = $financialEvent->getPostedDate();
            $posted_date = '';
            if (! is_null($postedDate)) {
                $posted_date = $postedDate->format('Y-m-d H:i:s');
            }

            $collection->push([
                'merchant_id' => $this->merchant_id,
                'merchant_store_id' => $this->merchant_store_id,
                'amazon_order_id' => $amazon_order_id,
                'seller_order_id' => $seller_order_id,
                'marketplace_name' => $marketplace_name,
                'order_charge_list' => $order_charge_list,
                'order_charge_adjustment_list' => $order_charge_adjustment_list,
                'shipment_fee_list' => $shipment_fee_list,
                'shipment_fee_adjustment_list' => $shipment_fee_adjustment_list,
                'shipment_item_adjustment_list' => $shipment_item_adjustment_list,
                'order_fee_list' => $order_fee_list,
                'order_fee_adjustment_list' => $order_fee_adjustment_list,
                'direct_payment_list' => $direct_payment_list,
                'posted_date' => $posted_date,
            ]);
        }
        return true;
    }
}
