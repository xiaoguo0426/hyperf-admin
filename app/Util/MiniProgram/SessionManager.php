<?php

declare(strict_types=1);

namespace App\Util\MiniProgram;

use Hyperf\Redis\RedisFactory;

class SessionManager
{
    public const SESSION_PREFIX = 'mini_program:';

    public static function set(string $channel, string $id, string $session): bool
    {
        $redis = di(RedisFactory::class)->get($channel);
        return $redis->setex(md5(self::SESSION_PREFIX . $channel . $id), 5 * 30, $session);
    }

    /**
     * @return bool|mixed|string
     */
    public static function get(string $channel, string $id)
    {
        $redis = di(RedisFactory::class)->get($channel);
        return $redis->get(md5(self::SESSION_PREFIX . $channel . $id));
    }

    public static function del(string $channel, string $id): int
    {
        $redis = di(RedisFactory::class)->get($channel);
        return $redis->del(md5(self::SESSION_PREFIX . $channel . $id));
    }
}
