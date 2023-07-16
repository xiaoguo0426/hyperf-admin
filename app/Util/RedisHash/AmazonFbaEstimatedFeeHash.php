<?php

namespace App\Util\RedisHash;

use App\Util\Prefix;

class AmazonFbaEstimatedFeeHash extends AbstractRedisHash
{
    public function __construct(int $merchant_id, int $merchant_store_id, string $currency)
    {
        $this->name = Prefix::amazonAsinFbaFee($merchant_id, $merchant_store_id, $currency);
        parent::__construct();
    }
}