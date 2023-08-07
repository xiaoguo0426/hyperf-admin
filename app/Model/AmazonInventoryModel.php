<?php

namespace App\Model;

/**
 * Class AmazonInventoryModel
 * @package App\Model
 * @property $id
 * @property $merchant_id
 * @property $merchant_store_id
 * @property $asin
 * @property $fn_sku
 * @property $product_name
 * @property $seller_sku
 * @property $total_quantity
 * @property $condition
 * @property $inventory_details
 * @property $last_updated_time
 * @property $country_ids
 * @property $created_at
 * @property $updated_at
 * @property $warehouse_condition_code
 * @property $restock_inv_recommendations
 * @property $afn_inbound
 * @property $inv_age
 * @property $inv_age_90days
 * @property $inv_age_90days_plus
 * @property $afn_fulfillable_quantity
 * @property $afn_unsellable_quantity
 * @property $mfn_listing_exists
 * @property $mfn_fulfillable_quantity
 * @property $afn_listing_exists
 * @property $afn_reserved_quantity
 * @property $fba_fee
 * @property $last_3days_average_sales
 * @property $last_7days_average_sales
 * @property $last_14days_average_sales
 * @property $last_30days_average_sales
 * @property $weighted_average_daily_sales
 * @property $shipped_quantity
 * @property $restock_fc_transfer
 * @property $available_sale_days
 * @property $replenishment_recommendation_60days
 */
class AmazonInventoryModel extends Model
{
    protected ?string $table = 'amazon_inventory';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = [
        'merchant_id',
        'merchant_store_id',
        'asin',
        'fn_sku',
        'seller_sku',
        'product_name',
        'condition',
        'fulfillable_quantity',
        'inbound_working_quantity',
        'inbound_shipped_quantity',
        'inbound_receiving_quantity',
        'total_reserved_quantity',
        'pending_customer_order_quantity',
        'pending_transshipment_quantity',
        'fc_processing_quantity',
        'total_researching_quantity',
        'researching_quantity_breakdown',
        'total_unfulfillable_quantity',
        'customer_damaged_quantity',
        'warehouse_damaged_quantity',
        'distributor_damaged_quantity',
        'carrier_damaged_quantity',
        'defective_quantity',
        'expired_quantity',
        'last_updated_time',
        'total_quantity',
        'country_ids'
    ];
}