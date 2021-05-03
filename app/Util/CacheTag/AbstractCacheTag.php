<?php


namespace App\Util\CacheTag;


abstract class AbstractCacheTag
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

    abstract public function genKey($unique): string;

    abstract public function check($code): bool;

    abstract public function cache($code): void;

    public function getError(): string
    {
        return $this->error;
    }
}