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

class AmazonFbaEstimatedFeeHash extends AbstractRedisHash
{
    public function __construct(int $merchant_id, int $merchant_store_id, string $currency)
    {
        $this->name = Prefix::amazonAsinFbaFee($merchant_id, $merchant_store_id, $currency);
        parent::__construct();
    }
}
