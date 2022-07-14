<?php

declare(strict_types=1);

namespace App\Util;

use App\Util\Limit\AbstractLimit;

class IpLimit extends AbstractLimit
{
    protected $maxTryCount = 3;

    public function genKey($unique): string
    {
        return Prefix::feedbackIpLimit($unique);
    }

    public function can(): bool
    {
        $can = $this->redis->get($this->key);

        if ($can === false) {
            $can = 0;
            $this->redis->set($this->key, $can, self::TTL);
        }

        if ($can >= $this->maxTryCount) {
            $this->error = 'You have reached the maximum number of submissions, please try again later.';
            return false;
        }
        $this->incr();

        return true;
    }

    public function incr(): int
    {
        return $this->redis->incr($this->key);
    }
}
