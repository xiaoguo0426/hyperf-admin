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
 * Class AmazonOrderItemModel.
 * @property $id
 * @property $merchant_id
 * @property $merchant_store_id
 * @property $seller_id
 * @property $order_id
 * @property $asin
 * @property $order_item_id
 * @property $seller_sku
 * @property $title
 * @property $quantity_ordered
 * @property $quantity_shipped
 * @property $product_info_number_of_items
 * @property $points_granted
 * @property $item_price
 * @property $shipping_price
 * @property $item_tax
 * @property $shipping_tax
 * @property $shipping_discount
 * @property $shipping_discount_tax
 * @property $promotion_discount
 * @property $promotion_discount_tax
 * @property $promotion_ids
 * @property $cod_fee
 * @property $cod_fee_discount
 * @property $is_gift
 * @property $condition_note
 * @property $condition_id
 * @property $condition_subtype_id
 * @property $scheduled_delivery_start_date
 * @property $scheduled_delivery_end_date
 * @property $price_designation
 * @property $tax_collection
 * @property $serial_number_required
 * @property $is_transparency
 * @property $ioss_number
 * @property $store_chain_store_id
 * @property $deemed_reseller_category
 * @property $buyer_info
 * @property $buyer_requested_cancel
 * @property $is_estimated_fba_fee
 * @property $fba_fee
 * @property $fba_fee_currency
 * @property $is_estimated_commission
 * @property $commission
 * @property $commission_currency
 * @property $created_at
 * @property $updated_at
 */
class AmazonOrderItemModel extends Model
{

    protected ?string $table = 'amazon_order_items';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';
}
