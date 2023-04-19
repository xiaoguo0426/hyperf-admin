<?php


namespace App\Util\RedisHash;


use App\Util\Prefix;

/**
 * Class AmazonAppHash
 * @package App\Util\RedisHash
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
 */
class AmazonAppHash extends AbstractRedisHash
{
    public function __construct(int $merchant_id, int $merchant_store_id)
    {
        $this->name = Prefix::amazonApp($merchant_id, $merchant_store_id);
        parent::__construct();
    }

    public function getIdAttr($value): int
    {
        return (int) $value;
    }

    public function getMerchantIdAttr($value): int
    {
        return (int) $value;
    }

    public function getMerchantStoreIdAttr($value): int
    {
        return (int) $value;
    }
}