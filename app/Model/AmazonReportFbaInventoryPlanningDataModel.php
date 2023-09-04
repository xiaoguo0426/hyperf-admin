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
 * Class AmazonReportFbaInventoryPlanningDataModel.
 * @property $id
 * @property $merchant_id
 * @property $merchant_store_id
 * @property $snapshot_date
 * @property $sku
 * @property $fnsku
 * @property $asin
 * @property $product_name
 * @property $condition
 * @property $available
 * @property $pending_removal_quantity
 * @property $inv_age_0_to_90_days
 * @property $inv_age_91_to_180_days
 * @property $inv_age_181_to_270_days
 * @property $inv_age_271_to_365_days
 * @property $inv_age_365_plus_days
 * @property $currency
 * @property $units_shipped_t7
 * @property $units_shipped_t30
 * @property $units_shipped_t60
 * @property $units_shipped_t90
 * @property $alert
 * @property $your_price
 * @property $sales_price
 * @property $lowest_price_new_plus_shipping
 * @property $lowest_price_used
 * @property $recommended_action
 * @property $healthy_inventory_level
 * @property $recommended_sales_price
 * @property $recommended_sale_duration_days
 * @property $recommended_removal_quantity
 * @property $estimated_cost_savings_of_recommended_actions
 * @property $sell_through
 * @property $item_volume
 * @property $volume_unit_measurement
 * @property $storage_type
 * @property $storage_volume
 * @property $marketplace
 * @property $product_group
 * @property $sales_rank
 * @property $days_of_supply
 * @property $estimated_excess_quantity
 * @property $weeks_of_cover_t30
 * @property $weeks_of_cover_t90
 * @property $featuredoffer_price
 * @property $sales_shipped_last_7_days
 * @property $sales_shipped_last_30_days
 * @property $sales_shipped_last_60_days
 * @property $sales_shipped_last_90_days
 * @property $inv_age_0_to_30_days
 * @property $inv_age_31_to_60_days
 * @property $inv_age_61_to_90_days
 * @property $inv_age_181_to_330_days
 * @property $inv_age_331_to_365_days
 * @property $estimated_storage_cost_next_month
 * @property $inbound_quantity
 * @property $inbound_working
 * @property $inbound_shipped
 * @property $inbound_received
 * @property $no_sale_last_6_months
 * @property $reserved_quantity
 * @property $unfulfillable_quantity
 * @property $quantity_to_be_charged_ais_181_210_days
 * @property $estimated_ais_181_210_days
 * @property $quantity_to_be_charged_ais_211_240_days
 * @property $estimated_ais_211_240_days
 * @property $quantity_to_be_charged_ais_241_270_days
 * @property $estimated_ais_241_270_days
 * @property $quantity_to_be_charged_ais_271_300_days
 * @property $estimated_ais_271_300_days
 * @property $quantity_to_be_charged_ais_301_330_days
 * @property $estimated_ais_301_330_days
 * @property $quantity_to_be_charged_ais_331_365_days
 * @property $estimated_ais_331_365_days
 * @property $quantity_to_be_charged_ais_365_PLUS_days
 * @property $estimated_ais_365_plus_days
 */
class AmazonReportFbaInventoryPlanningDataModel extends Model
{
    protected ?string $table = 'amazon_report_fba_inventory_planning_data';
}
