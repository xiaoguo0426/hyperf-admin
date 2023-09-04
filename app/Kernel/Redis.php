<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Kernel;

use Hyperf\Redis\RedisFactory;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Redis
{
    /**
     * @param mixed $group
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function get($group = 'default')
    {
        return di(RedisFactory::class)->get($group);
    }
}
