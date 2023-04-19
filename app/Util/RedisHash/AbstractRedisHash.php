<?php

declare(strict_types=1);

namespace App\Util\RedisHash;

use Hyperf\Contract\Arrayable;
use Hyperf\Contract\Jsonable;
use Hyperf\Redis\RedisFactory;
use Hyperf\Stringable\Str;

class AbstractRedisHash implements \ArrayAccess, Arrayable, Jsonable
{

    protected $key;

    protected $name = '';
    private $redis;

    public function __construct($connect = 'default')
    {
        $this->key = config('redis_array_prefix') . $this->name;
        $this->redis = di(RedisFactory::class)->get($connect);
    }

    /**
     * @throws \JsonException
     */
    public function __get($key)
    {
        $this->getAttr($key);
    }

    /**
     * @throws \JsonException
     */
    public function __set($name, $value)
    {
        $this->setAttr($name, $value);
    }

    public function __unset($name)
    {
        $this->offsetUnset($name);
    }

    /**
     * 判断属性是否存在
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->offsetExists($name);
    }

    public function __toString(): string
    {
        return $this->toJson();
    }

    /**
     * 判断属性是否存在
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->redis->hExists($this->key, (string) $offset);
    }

    /**
     * 获得属性
     * @param mixed $offset
     * @throws \JsonException
     * @return mixed
     */
    public function offsetGet($offset): mixed
    {
        $val = $this->redis->hGet($this->key, $offset);

        if ($val === false) {
            return null;
        }

        if (is_json($val)) {
            return json_decode($val, true, 512, JSON_THROW_ON_ERROR);
        }

        return $val;
    }

    /**
     * 设置属性
     * @param mixed $offset
     * @param mixed $value
     * @throws \JsonException
     */
    public function offsetSet($offset, $value): void
    {
        $this->setAttr($offset, $value);
    }

    /**
     * 删除属性
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        $this->redis->hDel($this->key, (string) $offset);
    }

    /**
     * 设置属性
     * @param string $offset
     * @param $value
     * @throws \JsonException
     * @return bool
     */
    public function setAttr(string $offset, $value): bool
    {
        if ('' === $offset) {
            throw new \RuntimeException('offset can not empty');
        }
        $name = $this->getRealFieldName($offset);
        // 检测修改器
        $method = 'set' . Str::studly($name) . 'Attr';

        if (method_exists($this, $method)) {
            $value = $this->$method($value);
        }
        return (bool) $this->redis->hSet($this->key, $offset, is_array($value) ? json_encode($value, JSON_THROW_ON_ERROR) : $value);
    }

    /**
     * 获得属性
     * @param string $offset
     * @throws \JsonException
     * @return mixed
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
            return $this->$method($value);
        }
        if (is_json($value)) {
            return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        }

        return $value;

    }

    protected function getRealFieldName(string $name): string
    {
        return Str::snake($name);
    }

    /**
     * 初始化
     * @param array $data
     * @return bool
     */
    public function init(array $data): bool
    {
        return $this->redis->hMSet($this->key, $data);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->redis->hGetAll($this->key);
    }

    /**
     * @throws \JsonException
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR) ?? '';
    }

    /**
     * 删除key
     * @return bool
     */
    public function destroy(): bool
    {
        return (bool) $this->redis->del($this->key);
    }

    /**
     * 设置有效期
     * @param $ttl
     * @return bool
     */
    public function ttl($ttl): bool
    {
        return $this->redis->expire($this->key, $ttl);
    }
}
