<?php

declare(strict_types=1);

namespace App\Util;

use Hyperf\Redis\RedisFactory;

class Redis
{
    public static function getInstance($connect = 'default')
    {
        return di(RedisFactory::class)->get($connect);
    }

    public static function changeDB($index)
    {
        $instance = self::getInstance();
        $instance->select($index);
        return $instance;
    }
}
