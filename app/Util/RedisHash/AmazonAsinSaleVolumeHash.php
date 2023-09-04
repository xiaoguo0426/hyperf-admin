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

class AmazonAsinSaleVolumeHash extends AbstractRedisHash
{
    public function __construct(int $merchant_id, string $type)
    {
        $this->name = Prefix::amazonAsinSaleVolume($merchant_id, $type);
        parent::__construct();
    }
}
