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
 * Class AmazonOrderModel.
 * @property $id
 * @property $merchant_id
 * @property $merchant_store_id
 * @property $seller_id
 * @property $amazon_order_id
 * @property $seller_order_id
 * @property $purchase_date
 * @property $last_update_date
 * @property $order_status
 * @property $fulfillment_channel
 * @property $sales_channel
 * @property $order_channel
 * @property $ship_service_level
 * @property $order_total_currency
 * @property $order_total_amount
 * @property $number_of_items_shipped
 * @property $number_of_items_unshipped
 * @property $payment_execution_detail
 * @property $payment_method
 * @property $payment_method_details
 * @property $marketplace_id
 * @property $shipment_service_level_category
 * @property $easy_ship_shipment_status
 * @property $cba_displayable_shipping_label
 * @property $order_type
 * @property $earliest_ship_date
 * @property $latest_ship_date
 * @property $earliest_delivery_date
 * @property $latest_delivery_date
 * @property $is_business_order
 * @property $is_prime
 * @property $is_premium_order
 * @property $is_global_express_enabled
 * @property $replaced_order_id
 * @property $is_replacement_order
 * @property $promise_response_due_date
 * @property $is_estimated_ship_date_set
 * @property $is_sold_by_ab
 * @property $is_iba
 * @property $default_ship_from_location_address
 * @property $buyer_invoice_preference
 * @property $buyer_tax_information
 * @property $fulfillment_instruction
 * @property $is_ispu
 * @property $is_access_point_order
 * @property $marketplace_tax_info
 * @property $seller_display_name
 * @property $shipping_address
 * @property $buyer_email
 * @property $buyer_info
 * @property $automated_shipping_settings
 * @property $has_regulated_items
 * @property $electronic_invoice_status
 * @property $created_at
 * @property $updated_at
 */
class AmazonOrderModel extends Model
{

    protected ?string $table = 'amazon_order';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';
}
