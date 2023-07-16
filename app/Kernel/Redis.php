<?php

namespace App\Kernel;

use Hyperf\Redis\RedisFactory;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Redis
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function get($group = 'default')
    {
        return di(RedisFactory::class)->get($group);
    }
}