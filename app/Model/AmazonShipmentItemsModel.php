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
 * Class AmazonShipmentItemsModel.
 * @property $id
 * @property $merchant_id
 * @property $merchant_store_id
 * @property $shipment_id
 * @property $seller_sku
 * @property $fulfillment_network_sku
 * @property $quantity_shipped
 * @property $quantity_received
 * @property $quantity_in_case
 * @property $release_date
 * @property $prep_details_list
 * @property $created_at
 * @property $updated_at
 */
class AmazonShipmentItemsModel extends Model
{

    protected ?string $table = 'amazon_shipment_items';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';
}
