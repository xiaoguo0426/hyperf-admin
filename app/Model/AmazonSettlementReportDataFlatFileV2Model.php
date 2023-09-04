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
 *  * @property $id
 * @property $merchant_id
 * @property $merchant_store_id
 * @property $date
 * @property $settlement_id
 * @property $settlement_start_date
 * @property $settlement_end_date
 * @property $deposit_date
 * @property $total_amount
 * @property $currency
 * @property $transaction_type
 * @property $order_id
 * @property $merchant_order_id
 * @property $adjustment_id
 * @property $shipment_id
 * @property $marketplace_name
 * @property $amount_type
 * @property $amount_description
 * @property $amount
 * @property $fulfillment_id
 * @property $posted_date
 * @property $posted_date_time
 * @property $order_item_code
 * @property $merchant_order_item_id
 * @property $merchant_adjustment_item_id
 * @property $sku
 * @property $quantity_purchased
 * @property $promotion_id
 * @property $created_at
 * @property $updated_at
 */
class AmazonSettlementReportDataFlatFileV2Model extends Model
{
    protected ?string $table = 'amazon_report_settlement_report_data_flat_file_v2';
}
