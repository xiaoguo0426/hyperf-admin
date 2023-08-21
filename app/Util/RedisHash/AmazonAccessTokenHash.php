<?php


namespace App\Util\RedisHash;


use App\Util\Prefix;

class AmazonAccessTokenHash extends AbstractRedisHash
{
    public function __construct(int $merchant_id, int $merchant_store_id, string $region)
    {
        $this->name = Prefix::amazonAccessToken($merchant_id, $merchant_store_id, $region);
        parent::__construct();
    }
}