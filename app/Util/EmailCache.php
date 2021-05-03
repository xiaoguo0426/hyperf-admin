<?php


namespace App\Util;


class EmailCache
{

    private $unique;

    private $redis;

    private $key;

    const TTL = 600;

    public function __construct($email)
    {
        $this->unique = $email;

        $this->key = $this->genKey($email);

        $this->redis = \App\Facade\Redis::instance();

    }

    private function genKey($email)
    {
        return Prefix::sendEmailCache($email);
    }

    /**
     * @param $code
     * @return bool
     */
    public function check($code)
    {
        $get = $this->redis->get($this->key);
        return $get ? ($get === $code) : false;
    }

    /**
     * @param $code
     * @return mixed
     */
    public function cache($code)
    {
        return $this->redis->set($this->key, $code, self::TTL);
    }


}