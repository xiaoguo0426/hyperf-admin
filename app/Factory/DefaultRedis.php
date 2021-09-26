<?php

declare(strict_types=1);

namespace App\Factory;

use Hyperf\Redis\Redis;

class DefaultRedis extends Redis
{
    protected $poolName = 'default';
}
