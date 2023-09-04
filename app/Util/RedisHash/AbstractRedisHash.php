<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\RedisHash;

use Hyperf\Contract\Arrayable;
use Hyperf\Contract\Jsonable;
use Hyperf\Redis\RedisFactory;
use Hyperf\Redis\RedisProxy;
use Hyperf\Stringable\Str;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class AbstractRedisHash implements \ArrayAccess, Arrayable, Jsonable
{
    protected string $key;

    protected string $name = '';

    private RedisProxy $redis;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct()
    {
        $this->key = \Hyperf\Config\config('app_name') . ':' . $this->name;

        $connect = 'default';
        $this->redis = di(RedisFactory::class)->get($connect);
    }

    /**
     * @param mixed $key
     * @return mixed
     * @throws \JsonException
     * @throws \RedisException
     */
    public function __get($key)
    {
        return $this->getAttr($key);
    }

    /**
     * @param mixed $name
     * @param mixed $value
     * @throws \JsonException
     * @throws \RedisException
     */
    public function __set($name, $value)
    {
        $this->setAttr($name, $value);
    }

    /**
     * @param mixed $name
     * @throws \RedisException
     */
    public function __unset($name)
    {
        $this->offsetUnset($name);
    }

    /**
     * 判断属性是否存在.
     * @param mixed $name
     * @return bool
     * @throws \RedisException
     */
    public function __isset($name)
    {
        return $this->offsetExists($name);
    }

    /**
     * @throws \JsonException
     * @throws \RedisException
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

    /**
     * 判断属性是否存在.
     * @param mixed $offset
     * @throws \RedisException
     */
    public function offsetExists($offset): bool
    {
        return $this->redis->hExists($this->key, (string) $offset);
    }

    /**
     * 获得属性.
     * @param mixed $offset
     * @throws \RedisException
     * @throws \JsonException
     */
    public function offsetGet($offset): mixed
    {
        return $this->getAttr($offset);
    }

    /**
     * 设置属性.
     * @param mixed $offset
     * @param mixed $value
     * @throws \JsonException
     * @throws \RedisException
     */
    public function offsetSet($offset, $value): void
    {
        $this->setAttr($offset, $value);
    }

    /**
     * 删除属性.
     * @param mixed $offset
     * @throws \RedisException
     */
    public function offsetUnset($offset): void
    {
        $this->redis->hDel($this->key, (string) $offset);
    }

    /**
     * 设置属性.
     * @param mixed $value
     * @throws \JsonException
     * @throws \RedisException
     */
    public function setAttr(string $offset, $value): bool
    {
        if ($offset === '') {
            throw new \RuntimeException('offset can not empty');
        }
        $name = $this->getRealFieldName($offset);
        // 检测修改器
        $method = 'set' . Str::studly($name) . 'Attr';

        if (method_exists($this, $method)) {
            $value = $this->{$method}($value);
        }
        return (bool) $this->redis->hSet($this->key, $offset, is_array($value) ? json_encode($value, JSON_THROW_ON_ERROR) : $value);
    }

    /**
     * 获得属性.
     * @throws \JsonException
     * @throws \RedisException
     */
    public function getAttr(string $offset): mixed
    {
        $value = $this->redis->hGet($this->key, $offset);

        if ($value === false) {
            return null;
        }

        $name = $this->getRealFieldName($offset);
        // 检测修改器
        $method = 'get' . Str::studly($name) . 'Attr';

        if (method_exists($this, $method)) {
            return $this->{$method}($value);
        }
        if (is_json($value)) {
            return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        }

        return $value;
    }

    /**
     * 初始化.
     * @throws \JsonException
     * @throws \RedisException
     */
    public function load(array $data): bool
    {
        foreach ($data as $key => $item) {
            $this->setAttr($key, $item);
        }
        return true;
    }

    /**
     * @throws \RedisException
     */
    public function toArray(): array
    {
        return $this->redis->hGetAll($this->key);
    }

    /**
     * 转json.
     * @throws \JsonException
     * @throws \RedisException
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR) ?? '';
    }

    /**
     * 删除hash缓存.
     * @throws \RedisException
     */
    public function destroy(): bool
    {
        return (bool) $this->redis->del($this->key);
    }

    /**
     * 设置有效期
     * @throws \RedisException
     */
    public function ttl(int $ttl): bool
    {
        return $this->redis->expire($this->key, $ttl);
    }

    protected function getRealFieldName(string $name): string
    {
        return Str::snake($name);
    }
}
