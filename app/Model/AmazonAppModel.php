<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Model;

use App\Util\RegionRefreshTokenConfig;

/**
 * Class AmazonAppModel.
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
 * @property $created_at
 * @property $updated_at
 */
class AmazonAppModel extends Model
{
    protected ?string $table = 'amazon_app';

    /**
     * @param mixed $value
     * @throws \JsonException
     */
    public function getConfigAttribute($value): array
    {
        //        $data = [];
        //        $decodes = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        //        foreach ($decodes as $region => $json) {
        //            $data[$region] = new RegionRefreshTokenConfig($json['region'], $json['country_ids'], $json['refresh_token']);
        //        }
        //        return $data;
        return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param mixed $configs
     * @throws \JsonException
     */
    public function setConfigAttribute($configs): void
    {
        //        $data = [];
        //        foreach ($configs as $region => $config) {
        //            $data[$region] = json_encode($config, JSON_THROW_ON_ERROR);
        //        }
        //        $this->attributes['config'] = json_encode($data, JSON_THROW_ON_ERROR);
        $this->attributes['config'] = json_encode($configs, JSON_THROW_ON_ERROR);
    }

    /**
     * @return RegionRefreshTokenConfig[]
     */
    public function getRegionRefreshTokenConfigs(): array
    {
        $configs = [];
        foreach ($this->config as $region => $data) {
            if ($data['region'] && $data['country_ids'] && $data['refresh_token']) {
                $configs[$region] = new RegionRefreshTokenConfig($data['region'], $data['country_ids'], $data['refresh_token']);
            }
        }
        return $configs;
    }
}
