<?php


namespace App\Util;


use App\Util\Limit\AbstractLimit;

class IpLimit extends AbstractLimit
{
    protected $maxTryCount = 3;

    public function genKey($unique)
    {
        return Prefix::feedbackIpLimit($unique);
    }

    public function can()
    {
        $can = $this->redis->get($this->key);

        if (false === $can) {
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

    public function incr()
    {
        return $this->redis->incr($this->key);
    }
}