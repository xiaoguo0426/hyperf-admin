<?php

namespace App\Util\RedisHash;

use App\Contracts\Arrayable;
use App\Contracts\Jsonable;
use Hyperf\Redis\RedisFactory;

class AbstractRedisHash implements \ArrayAccess, Arrayable, Jsonable
{

    private $redis;

    private $key;

    protected $name = '';

    public function __construct($connect = 'default')
    {
        $this->key = config('redis_array_prefix') . $this->name;
        $this->redis = di(RedisFactory::class)->get($connect);
    }

    /**
     * 判断属性是否存在
     * @param mixed $offset
     * @return bool|void
     */
    public function offsetExists($offset)
    {
        $this->redis->hExists($this->key, (string)$offset);
    }

    /**
     * 获得属性
     * @param mixed $offset
     * @return mixed|string|null
     */
    public function offsetGet($offset)
    {
        $val = $this->redis->hGet($this->key, $offset);

        if (false === $val) {
            return null;
        }

        if (is_json($val)) {
            return json_decode($val, true);
        }

        return $val;
    }

    /**
     * 设置属性
     * @param mixed $offset
     * @param mixed $value
     * @return bool|int|void
     */
    public function offsetSet($offset, $value)
    {
        return (bool)$this->redis->hSet($this->key, (string)$offset, is_array($value) ? json_encode($value) : $value);
    }

    /**
     * 删除属性
     * @param mixed $offset
     * @return bool|int|void
     */
    public function offsetUnset($offset)
    {
        return $this->redis->hDel($this->key, (string)$offset);
    }

    public function __get($key)
    {
        return $this->offsetGet($key);
    }

    public function __set($name, $value)
    {
        return $this->offsetSet($name, $value);
    }

    public function __unset($name)
    {
        return $this->offsetUnset($name);
    }

    public function __isset($name)
    {
        return $this->offsetExists($name);
    }

    public function __set_state()
    {
    }

    /**
     * 初始化
     * @param array $data
     * @return bool
     */
    public function init(array $data)
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
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray()) ?? '';
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

}