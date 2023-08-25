<?php

namespace App\Util;

use JetBrains\PhpStorm\ArrayShape;
use JsonException;
use JsonSerializable;

class RegionRefreshTokenConfig implements JsonSerializable
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

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'region' => $this->region,
            'country_ids' => implode(',', $this->country_ids),
            'refresh_token' => $this->refresh_token,
        ];
    }

    /**
     * @throws JsonException
     */
    public static function fromJson($json): RegionRefreshTokenConfig
    {
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR); // 解码为关联数组
        return new self($data['region'], $data['country_ids'], $data['refresh_token']);
    }

}