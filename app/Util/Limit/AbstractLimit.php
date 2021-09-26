<?php

declare(strict_types=1);

namespace App\Util\Limit;

abstract class AbstractLimit
{

    public const TTL = 3600;

    protected $redis;

    protected $key;

    protected $error = '';

    protected $maxTryCount = 5;
    private $unique;

    public function __construct($unique)
    {
        $this->unique = $unique;

        $this->key = $this->genKey($unique);

        $this->redis = \App\Facade\Redis::instance();
    }

    abstract public function genKey($unique): void;

    abstract public function can(): void;

    abstract public function incr(): void;

    public function getError()
    {
        return $this->error;
    }
}
