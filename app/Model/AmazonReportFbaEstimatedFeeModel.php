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
 * @property $country_id
 * @property $sku
 * @property $fn_sku
 * @property $asin
 * @property $product_name
 * @property $product_group
 * @property $brand
 * @property $fulfilled_by
 * @property $your_price
 * @property $sales_price
 * @property $longest_side
 * @property $median_side
 * @property $shortest_side
 * @property $length_and_girth
 * @property $unit_of_dimension
 * @property $item_package_weight
 * @property $unit_of_weight
 * @property $product_size_tier
 * @property $currency
 * @property $estimated_fee_total
 * @property $estimated_referral_fee_per_unit
 * @property $estimated_wariable_closing_fee
 * @property $estimated_order_handing_fee_per_order
 * @property $estimated_pick_pack_fee_per_unit
 * @property $estimated_weight_handling_fee_per_unit
 * @property $expected_fulfillment_fee_per_unit
 * @property $created_at
 * @property $updated_at
 */
class AmazonReportFbaEstimatedFeeModel extends Model
{
    protected ?string $table = 'amazon_report_fba_estimated_fee';
}
