<?php


namespace App\Util\Limit;


Abstract class AbstractLimit
{
    private $unique;

    protected $redis;

    protected $key;

    protected $error = '';

    const TTL = 3600;

    protected $maxTryCount = 5;

    public function __construct($unique)
    {
        $this->unique = $unique;

        $this->key = $this->genKey($unique);

        $this->redis = \App\Facade\Redis::instance();
    }

    abstract public function genKey($unique);

    abstract public function can();

    abstract public function incr();

    public function getError()
    {
        return $this->error;
    }

}