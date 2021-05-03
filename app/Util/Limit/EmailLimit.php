<?php


namespace App\Util\Limit;


class EmailLimit extends AbstractLimit
{

    protected $maxTryCount = 5;

    public function genKey($unique)
    {
        return '';
    }

    public function can()
    {
        $canSend = $this->redis->get($this->key);

        if (false === $canSend) {
            $canSend = 0;
            $this->redis->set($this->key, $canSend, self::TTL);
        }

        if ($canSend >= $this->maxTryCount) {
            $this->error = 'Attempts reached the limit，please try again later！';
            return false;
        }
        return true;
    }

    public function incr(): void
    {
        $this->redis->incr($this->key);
    }
}