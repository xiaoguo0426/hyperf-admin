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
 * Class AmazonReportFbaMyiUnsuppressedInventoryDataModel.
 * @property $id
 * @property $merchant_id
 * @property $merchant_store_id
 * @property $sku
 * @property $fnsku
 * @property $asin
 * @property $product_name
 * @property $condition
 * @property $your_price
 * @property $mfn_listing_exists
 * @property $mfn_fulfillable_quantity
 * @property $afn_listing_exists
 * @property $afn_warehouse_quantity
 * @property $afn_fulfillable_quantity
 * @property $afn_unsellable_quantity
 * @property $afn_reserved_quantity
 * @property $afn_total_quantity
 * @property $per_unit_volume
 * @property $afn_inbound_working_quantity
 * @property $afn_inbound_shipped_quantity
 * @property $afn_inbound_receiving_quantity
 * @property $afn_researching_quantity
 * @property $afn_reserved_future_supply
 * @property $afn_future_supply_buyable
 * @property $created_at
 * @property $updated_at
 */
class AmazonReportFbaMyiUnsuppressedInventoryDataModel extends Model
{
    protected ?string $table = 'amazon_report_fba_myi_unsuppressed_inventory_data';
}
