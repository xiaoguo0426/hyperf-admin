<?php


namespace App\Facade;


use Hyperf\Server\Exception\RuntimeException;
use Hyperf\Utils\ApplicationContext;

abstract class Facade
{
    protected static function getFacadeAccessor()
    {
        throw new RuntimeException('Facade does not implement getFacadeAccessor method.');
    }

    // make 参数
    protected static function getResolveAccessor(): array
    {
        return [];
    }

    // 单例模式
    protected static function singleton(): bool
    {
        return true;
    }

    // 获取实例
    public static function instance()
    {
        return static::singleton() ?
            static::container()->get(static::getFacadeAccessor()) :
            static::container()->make(static::getFacadeAccessor(), static::getResolveAccessor());
    }

    // 容器实例
    public static function container(): \Psr\Container\ContainerInterface
    {
        return ApplicationContext::getContainer();
    }

    // 静态访问
    public static function __callStatic($method, $args)
    {
        $instance = static::instance();

        if (! $instance) {
            throw new RuntimeException('A facade root has not been set.');
        }

        return $instance->$method(...$args);
    }

}