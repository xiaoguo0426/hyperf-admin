<?php
declare(strict_types=1);

namespace App\Util;

/**
 * @property string scopes 权限范围 Constants::SCOPE_ROLE 访问权限  Constants::SCOPE_REFRESH 刷新权限
 */
class Payload implements \ArrayAccess
{

    private $container;

    public function __construct($container = [])
    {
        $this->container = $container;
    }

    /**
     * 以对象的方式访问数组中的数据
     *
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->container[$key];
    }

    /**
     * 以对象方式添加一个数组元素
     *
     * @param $key
     * @param $val
     */
    public function __set($key, $val)
    {
        $this->container[$key] = $val;
    }

    /**
     * 以对象方式判断数组元素是否设置
     *
     * @param $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->container[$key]);
    }

    /**
     * 以对象方式删除一个数组元素
     *
     * @param $key
     */
    public function __unset($key)
    {
        unset($this->container[$key]);
    }

    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset): bool
    {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset): void
    {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->container[$offset] ?? null;
    }

    public function toArray(): array
    {
        return $this->container;
    }


}