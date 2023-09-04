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
 * @property $marketplace_id
 * @property $data_time
 * @property $ordered_product_sales_amount
 * @property $ordered_product_sales_currency_code
 * @property $ordered_product_sales_b2b_amount
 * @property $ordered_product_sales_b2b_currency_code
 * @property $units_ordered
 * @property $units_ordered_b2b
 * @property $total_order_items
 * @property $total_order_items_b2b
 * @property $average_sales_per_order_item_amount
 * @property $average_sales_per_order_item_currency_code
 * @property $average_sales_per_order_item_b2b_amount
 * @property $average_sales_per_order_item_b2b_currency_code
 * @property $average_units_per_order_item
 * @property $average_units_per_order_item_b2b
 * @property $average_selling_price_amount
 * @property $average_selling_price_currency_code
 * @property $average_selling_price_b2b_amount
 * @property $average_selling_price_b2b_currency_code
 * @property $units_refunded
 * @property $refund_rate
 * @property $claims_granted
 * @property $claims_amount_amount
 * @property $claims_amount_currency_code
 * @property $shipped_product_sales_amount
 * @property $shipped_product_sales_currency_code
 * @property $units_shipped
 * @property $orders_shipped
 * @property $browser_page_views
 * @property $browser_page_views_b2b
 * @property $mobile_app_page_views
 * @property $mobile_app_page_views_b2b
 * @property $page_views
 * @property $page_views_b2b
 * @property $browser_sessions
 * @property $browser_sessions_b2b
 * @property $mobile_app_sessions
 * @property $mobile_app_sessions_b2b
 * @property $sessions
 * @property $sessions_b2b
 * @property $buy_box_percentage
 * @property $buy_box_percentage_b2b
 * @property $order_item_session_percentage
 * @property $order_item_session_percentage_b2b
 * @property $unit_session_percentage
 * @property $unit_session_percentage_b2b
 * @property $average_offer_count
 * @property $average_parent_items
 * @property $feedback_received
 * @property $negative_feedback_received
 * @property $received_negative_feedback_rate
 * @property $created_at
 * @property $updated_at
 */
class AmazonReportSalesAndTrafficByDateModel extends Model
{
    protected ?string $table = 'amazon_report_sales_and_traffic_by_date';
}
