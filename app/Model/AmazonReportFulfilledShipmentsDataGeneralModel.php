<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Model;

/**
 * @property $id
 * @property $merchant_id
 * @property $merchant_store_id
 * @property $amazon_order_id
 * @property $merchant_order_id
 * @property $shipment_id
 * @property $shipment_item_id
 * @property $amazon_order_item_id
 * @property $merchant_order_item_id
 * @property $purchase_date
 * @property $payments_date
 * @property $shipment_date
 * @property $reporting_date
 * @property $buyer_email
 * @property $buyer_name
 * @property $buyer_phone_number
 * @property $sku
 * @property $product_name
 * @property $quantity_shipped
 * @property $currency
 * @property $item_price
 * @property $item_tax
 * @property $shipping_price
 * @property $shipping_tax
 * @property $gift_wrap_price
 * @property $gift_wrap_tax
 * @property $ship_service_level
 * @property $recipient_name
 * @property $ship_address_1
 * @property $ship_address_2
 * @property $ship_address_3
 * @property $ship_city
 * @property $ship_state
 * @property $ship_postal_code
 * @property $ship_country
 * @property $ship_phone_number
 * @property $bill_address_1
 * @property $bill_address_2
 * @property $bill_address_3
 * @property $bill_city
 * @property $bill_state
 * @property $bill_postal_code
 * @property $bill_country
 * @property $item_promotion_discount
 * @property $ship_promotion_discount
 * @property $carrier
 * @property $tracking_number
 * @property $estimated_arrival_date
 * @property $fulfillment_center_id
 * @property $fulfillment_channel
 * @property $sales_channel
 * @property $created_at
 * @property $updated_at
 */
class AmazonReportFulfilledShipmentsDataGeneralModel extends Model
{
    protected ?string $table = 'amazon_report_amazon_fulfilled_shipments_data_general';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';
}
