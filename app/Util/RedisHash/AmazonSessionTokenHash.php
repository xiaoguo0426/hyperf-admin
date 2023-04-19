<?php


namespace App\Util\RedisHash;


use App\Util\Prefix;

/**
 * Class AmazonSessionTokenHash
 * @package App\Util\RedisHash
 * @property $accessKeyId
 * @property $secretAccessKey
 * @property $sessionToken
 * @property $expiration
 */
class AmazonSessionTokenHash extends AbstractRedisHash
{
    public function __construct(int $merchant_id, int $merchant_store_id)
    {
        $this->name = Prefix::amazonSessionToken($merchant_id, $merchant_store_id);
        parent::__construct();
    }
}