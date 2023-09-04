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
 * Class AmazonSalesAndTrafficByAsinModel.
 * @property $id
 * @property $merchant_id
 * @property $merchant_store_id
 * @property $marketplace_id
 * @property $data_time
 * @property $parent_asin
 * @property $child_asin
 * @property $units_ordered
 * @property $units_ordered_b2b
 * @property $ordered_product_sales_amount
 * @property $ordered_product_sales_currency_code
 * @property $ordered_product_sales_b2b_amount
 * @property $ordered_product_sales_b2b_amount_currency_code
 * @property $total_order_items
 * @property $total_order_items_b2b
 * @property $browser_sessions
 * @property $browser_sessions_b2b
 * @property $mobile_app_sessions
 * @property $mobile_app_sessions_b2b
 * @property $sessions
 * @property $sessions_b2b
 * @property $browser_session_percentage
 * @property $browser_session_percentage_b2b
 * @property $mobile_app_session_percentage
 * @property $mobile_app_session_percentage_b2b
 * @property $session_percentage
 * @property $session_percentage_b2b
 * @property $browser_page_views
 * @property $browser_page_views_b2b
 * @property $mobile_app_page_views
 * @property $mobile_app_page_views_b2b
 * @property $page_views
 * @property $page_views_b2b
 * @property $browser_page_views_percentage
 * @property $browser_page_views_percentage_b2b
 * @property $mobile_app_page_views_percentage
 * @property $mobile_app_page_views_percentage_b2b
 * @property $page_views_percentage
 * @property $page_views_percentage_b2b
 * @property $buy_box_percentage
 * @property $buy_box_percentage_b2b
 * @property $unit_session_percentage
 * @property $unit_session_percentage_b2b
 * @property $created_at
 * @property $updated_at
 */
class AmazonReportSalesAndTrafficByAsinModel extends Model
{
    protected ?string $table = 'amazon_report_sales_and_traffic_by_asin';
}
