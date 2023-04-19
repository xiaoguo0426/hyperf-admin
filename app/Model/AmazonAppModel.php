<?php

namespace App\Model;

/**
 * Class AmazonAppModel
 * @package App\Model
 * @property $id
 * @property $merchant_id
 * @property $merchant_store_id
 * @property $seller_id
 * @property $app_id
 * @property $app_name
 * @property $aws_access_key
 * @property $aws_secret_key
 * @property $user_arn
 * @property $role_arn
 * @property $lwa_client_id
 * @property $lwa_client_id_secret
 * @property $region
 * @property $country_ids
 * @property $refresh_token
 * @property $status
 * @property $created_at
 * @property $updated_at
 */
class AmazonAppModel extends Model
{
    protected ?string $table = 'amazon_app';
}