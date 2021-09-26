<?php

declare(strict_types=1);

namespace App\Util\CacheTag;

class CacheTag extends AbstractCacheTag
{
    public const TTL = 600;

    public function genKey($unique): string
    {
        return $unique;
    }

    public function check($code): bool
    {
        $get = $this->redis->get($this->key);
        return $get ? ($get === $code) : false;
    }

    /**
     * @param $code
     *
     * @return mixed
     */
    public function cache($code): void
    {
        $this->redis->set($this->key, $code, self::TTL);
    }
}
