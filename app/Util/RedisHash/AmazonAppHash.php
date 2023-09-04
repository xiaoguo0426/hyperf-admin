<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\RedisHash;

use App\Util\Prefix;
use App\Util\RegionRefreshTokenConfig;

/**
 * Class AmazonAppHash.
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
 * @property $config
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

    public function getStatusAttr($value): int
    {
        return (int) $value;
    }

    /**
     * @param mixed $value
     * @throws \JsonException
     */
    public function getConfigAttr($value)
    {
        //        $config = [];
        //        $list = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        //        foreach ($list as $key => $item) {
        //            if ($item['country_ids'] && $item['refresh_token']) {
        //                $config[$key] = new RegionRefreshTokenConfig($item['region'], $item['country_ids'], $item['refresh_token']);
        //            }
        //        }
        //
        //        return $config;
        return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param mixed $value
     * @throws \JsonException
     */
    public function setConfigAttr($value): bool|string
    {
        return json_encode($value, JSON_THROW_ON_ERROR);
    }
}
