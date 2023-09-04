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

/**
 * Class AmazonSessionTokenHash.
 * @property $accessKeyId
 * @property $secretAccessKey
 * @property $sessionToken
 * @property $expiration
 */
class AmazonSessionTokenHash extends AbstractRedisHash
{
    public function __construct(int $merchant_id, int $merchant_store_id, string $region)
    {
        $this->name = Prefix::amazonSessionToken($merchant_id, $merchant_store_id, $region);
        parent::__construct();
    }
}
