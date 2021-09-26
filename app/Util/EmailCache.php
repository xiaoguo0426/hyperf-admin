<?php

declare(strict_types=1);

namespace App\Util;

class EmailCache
{

    public const TTL = 600;
    private $unique;

    private $redis;

    private $key;

    public function __construct($email)
    {
        $this->unique = $email;

        $this->key = $this->genKey($email);

        $this->redis = \App\Facade\Redis::instance();
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
    public function cache($code)
    {
        return $this->redis->set($this->key, $code, self::TTL);
    }

    private function genKey($email)
    {
        return Prefix::sendEmailCache($email);
    }
}
