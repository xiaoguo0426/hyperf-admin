<?php
declare(strict_types=1);

namespace App\Util;

use Hyperf\Redis\RedisFactory;

class Redis
{

    public static function getInstance($db = 'default')
    {
        return di(RedisFactory::class)->get($db);
    }

}