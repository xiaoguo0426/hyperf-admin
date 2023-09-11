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
 * Class AmazonShipmentModel.
 * @property $id
 * @property $merchant_id
 * @property $merchant_store_id
 * @property $marketplace_id
 * @property $shipment_id
 * @property $shipment_name
 * @property $shipment_from_address
 * @property $destination_fulfillment_center_id
 * @property $shipment_status
 * @property $label_prep_type
 * @property $are_cases_required
 * @property $confirmed_need_by_date
 * @property $box_contents_source
 * @property $total_units
 * @property $fee_per_unit_currency
 * @property $fee_per_unit_value
 * @property $total_fee_currency
 * @property $total_fee_value
 * @property $created_at
 * @property $updated_at
 */
class AmazonShipmentModel extends Model
{

    protected ?string $table = 'amazon_shipment';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';
}
