<?php

declare(strict_types=1);

namespace App\Util;

class RedisLock
{
    /**
     * 缓存 Redis 连接
     *
     * @return \Hyperf\Redis\RedisProxy|mixed|\Redis|String|null
     */
    public static function getRedis()
    {
        return Redis::getInstance();
    }

    /**
     * 获得锁,如果锁被占用,阻塞,直到获得锁或者超时。
     * -- 1、如果 $timeout 参数为 0,则立即返回锁。
     * -- 2、建议 timeout 设置为 0,避免 redis 因为阻塞导致性能下降。请根据实际需求进行设置。
     *
     * @param string $key 缓存KEY。
     * @param int $timeout 取锁超时时间。单位(秒)。等于0,如果当前锁被占用,则立即返回失败。如果大于0,则反复尝试获取锁直到达到该超时时间。
     * @param int $lockSecond 锁定时间。单位(秒)。
     * @param int $sleep 取锁间隔时间。单位(微秒)。当锁为占用状态时。每隔多久尝试去取锁。默认 0.1 秒一次取锁。
     *
     * @return bool 成功:true、失败:false
     */
    public static function lock(string $key, int $timeout = 0, int $lockSecond = 20, int $sleep = 100000): bool
    {
        if ($key === '') {
            throw new \Exception('缓存KEY没有设置');
        }
        if (! is_int($timeout) || $timeout < 0) {
            throw new \Exception('timeout 参数设置有误');
        }
        $start = self::getMicroTime();
        $redis = self::getRedis();
        do {
            // [1] 锁的 KEY 不存在时设置其值并把过期时间设置为指定的时间。锁的值并不重要。重要的是利用 Redis 的特性。
            $acquired = $redis->set("Lock:{$key}", 1, ['NX', 'EX' => $lockSecond]);
            if ($acquired) {
                break;
            }
            if ($timeout === 0) {
                break;
            }
            usleep($sleep);
        } while (self::getMicroTime() < $start + ($timeout * 1000000));
        return $acquired ? true : false;
    }

    /**
     * 释放锁
     *
     * @param $key
     *
     * @throws \Exception
     */
    public static function release($key): void
    {
        if ($key === '') {
            // 请更换为自己项目抛异常的方法。
            throw new \Exception('缓存KEY没有设置');
        }
        $redis = self::getRedis();
        $redis->del("Lock:{$key}");
    }

    /**
     * 获取当前微秒
     */
    protected static function getMicroTime(): int
    {
        return (int) bcmul(microtime(true), 1000000);
    }
}
