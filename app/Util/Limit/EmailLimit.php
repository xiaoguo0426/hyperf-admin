<?php

declare(strict_types=1);

namespace App\Util\Limit;

class EmailLimit extends AbstractLimit
{
    protected $maxTryCount = 5;

    public function genKey($unique): string
    {
        return '';
    }

    public function can(): bool
    {
        $canSend = $this->redis->get($this->key);

        if ($canSend === false) {
            $canSend = 0;
            $this->redis->set($this->key, $canSend, self::TTL);
        }

        if ($canSend >= $this->maxTryCount) {
            $this->error = 'Attempts reached the limit，please try again later！';
            return false;
        }
        return true;
    }

    public function incr(): int
    {
        return $this->redis->incr($this->key);
    }
}
