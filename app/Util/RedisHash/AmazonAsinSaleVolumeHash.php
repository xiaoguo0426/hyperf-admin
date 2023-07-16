<?php

namespace App\Util\RedisHash;

use App\Util\Prefix;

class AmazonAsinSaleVolumeHash extends AbstractRedisHash
{
    public function __construct(int $merchant_id, string $type)
    {
        $this->name = Prefix::amazonAsinSaleVolume($merchant_id, $type);
        parent::__construct();
    }
}