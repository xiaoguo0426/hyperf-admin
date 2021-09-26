<?php

declare(strict_types=1);

namespace App\Util;

class LoginErrLimit
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

    public function getMaxTryCount()
    {
        return $this->maxTryCount;
    }

    public function getError()
    {
        return $this->error;
    }

    public function canLogin()
    {
        $max_count = 5;//可重试次数

        $login_err_count = $this->getCount();

        if ($login_err_count === false) {
            $login_err_count = 0;
            $this->redis->set($this->key, $login_err_count, self::TTL);
        }

        if ($login_err_count >= $max_count) {
            $this->error = 'Attempts reached the limit，Try again in ' . (self::TTL / 3600) . ' hour！';
            return false;
        }
        return true;
    }

    public function getCount()
    {
        return $this->redis->get($this->key);
    }

    /**
     * 增加一次
     */
    public function incr(): void
    {
        $incr = $this->redis->incr($this->key);

        $diff = $this->maxTryCount - $incr;

        $this->error = $diff ? 'Incorrect username or password！' : 'Attempts reached the limit！';
    }

    /**
     * 清除
     */
    public function clear()
    {
        return $this->redis->del($this->key);
    }

    private function genKey($unique)
    {
        return Prefix::getLoginErrCount($unique);
    }
}
