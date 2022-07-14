<?php

declare(strict_types=1);

namespace App\Util;

class EmailLimit
{

    public const TTL = 3600;
    private $unique;

    private $redis;

    private $key;

    private $error = '';

    private $maxTryCount = 5;

    public function __construct($unique)
    {
        $this->unique = $unique;

        $this->key = $this->genKey($unique);

        $this->redis = \App\Facade\Redis::instance();
    }

    public function can(): bool
    {
        $canSend = $this->redis->get($this->key);

        if ($canSend === false) {
            $canSend = 0;
            $this->redis->set($this->key, $canSend, self::TTL);
        }

        if ($canSend >= $this->maxTryCount) {
            $this->error = '尝试次数达到上限，锁定' . (self::TTL / 3600) . '小时内禁止登录！';
            return false;
        }
        return true;
    }

    /**
     * 增加一次
     */
    public function incr(): int
    {
        return $this->redis->incr($this->key);
    }

    public function genKey($unique): string
    {
        return Prefix::getSendEmailLimit($unique);
    }
}
