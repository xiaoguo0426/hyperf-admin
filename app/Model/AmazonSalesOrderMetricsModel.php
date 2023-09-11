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
 * Class AmazonSalesOrderMetricsModel.
 * @property $id
 * @property $merchant_id
 * @property $merchant_store_id
 * @property $marketplace_id
 * @property $interval_type
 * @property $interval
 * @property $unit_count
 * @property $order_count
 * @property $order_item_count
 * @property $avg_unit_price_currency_code
 * @property $avg_unit_price
 * @property $total_sales_currency_code
 * @property $total_sales_amount
 * @property $created_at
 * @property $updated_at
 */
class AmazonSalesOrderMetricsModel extends Model
{
    protected ?string $table = 'amazon_sales_order_metrics';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

}
