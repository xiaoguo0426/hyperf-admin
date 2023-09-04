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
 * @property $return_date
 * @property $order_id
 * @property $sku
 * @property $fnsku
 * @property $asin
 * @property $product_name
 * @property $quantity
 * @property $fulfillment_center_id
 * @property $detailed_disposition
 * @property $reason
 * @property $status
 * @property $license_plate_number
 * @property $customer_comments
 * @property $created_at
 * @property $updated_at
 */
class AmazonReportFbaFulfillmentCustomerReturnDataModel extends Model
{
    protected ?string $table = 'amazon_report_fba_fulfillment_customer_return_data';
}
