<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util;

class RegionRefreshTokenConfig implements \JsonSerializable
{
    private string $region;

    private array $country_ids;

    private string $refresh_token;

    public function __construct(string $region, string $country_ids, string $refresh_token)
    {
        $this->region = $region;

        $this->country_ids = explode(',', $country_ids);

        $this->refresh_token = $refresh_token;
    }

    public function region(): string
    {
        return $this->region;
    }

    public function countryIds(): array
    {
        return $this->country_ids;
    }

    public function refreshToken(): string
    {
        return $this->refresh_token;
    }

    public function jsonSerialize(): array
    {
        return [
            'region' => $this->region,
            'country_ids' => implode(',', $this->country_ids),
            'refresh_token' => $this->refresh_token,
        ];
    }

    /**
     * @param mixed $json
     * @throws \JsonException
     */
    public static function fromJson($json): RegionRefreshTokenConfig
    {
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR); // 解码为关联数组
        return new self($data['region'], $data['country_ids'], $data['refresh_token']);
    }
}
