<?php

declare(strict_types=1);

namespace App\Factory;

use Hyperf\Redis\Redis;

class TestRedis extends Redis
{
    protected $poolName = 'test';
}
