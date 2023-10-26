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
 * Class AmazonInventoryModel.
 * @property $id
 * @property $merchant_id
 * @property $merchant_store_id
 * @property $asin
 * @property $fn_sku
 * @property $seller_sku
 * @property $product_name
 * @property $main_image
 * @property $product_type
 * @property $created_date
 * @property $last_updated_date
 * @property $condition
 * @property $fulfillable_quantity
 * @property $inbound_working_quantity
 * @property $inbound_shipped_quantity
 * @property $inbound_receiving_quantity
 * @property $total_reserved_quantity
 * @property $pending_customer_order_quantity
 * @property $pending_transshipment_quantity
 * @property $fc_processing_quantity
 * @property $total_researching_quantity
 * @property $researching_quantity_in_short_term
 * @property $researching_quantity_in_mid_term
 * @property $researching_quantity_in_long_term
 * @property $total_unfulfillable_quantity
 * @property $customer_damaged_quantity
 * @property $warehouse_damaged_quantity
 * @property $distributor_damaged_quantity
 * @property $carrier_damaged_quantity
 * @property $defective_quantity
 * @property $expired_quantity
 * @property $last_updated_time
 * @property $total_quantity
 * @property $country_ids
 * @property $created_at
 * @property $updated_at
 */
class AmazonInventoryModel extends Model
{

    protected ?string $table = 'amazon_inventory';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

//    /**
//     * The attributes that are mass assignable.
//     */
//    protected array $fillable = [
//        'merchant_id',
//        'merchant_store_id',
//        'asin',
//        'fn_sku',
//        'seller_sku',
//        'product_name',
//        'condition',
//        'fulfillable_quantity',
//        'inbound_working_quantity',
//        'inbound_shipped_quantity',
//        'inbound_receiving_quantity',
//        'total_reserved_quantity',
//        'pending_customer_order_quantity',
//        'pending_transshipment_quantity',
//        'fc_processing_quantity',
//        'total_researching_quantity',
//        'researching_quantity_breakdown',
//        'total_unfulfillable_quantity',
//        'customer_damaged_quantity',
//        'warehouse_damaged_quantity',
//        'distributor_damaged_quantity',
//        'carrier_damaged_quantity',
//        'defective_quantity',
//        'expired_quantity',
//        'last_updated_time',
//        'total_quantity',
//        'country_ids',
//    ];
}
