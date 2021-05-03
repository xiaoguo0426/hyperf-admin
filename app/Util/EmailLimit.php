<?php

namespace App\Util;

class EmailLimit
{

    private $unique;

    private $redis;

    private $key;

    private $error = '';

    const TTL = 3600;

    private $maxTryCount = 5;

    public function __construct($unique)
    {
        $this->unique = $unique;

        $this->key = $this->genKey($unique);

        $this->redis = \App\Facade\Redis::instance();

    }

    private function genKey($unique)
    {
        return Prefix::getSendEmailLimit($unique);
    }

    public function canSend()
    {
        $canSend = $this->redis->get($this->key);

        if (false === $canSend) {
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
    public function incr(): void
    {
        $this->redis->incr($this->key);
    }

}