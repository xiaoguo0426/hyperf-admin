<?php


namespace App\Facade;


use Hyperf\Redis\RedisFactory;

class Redis extends Facade
{
    protected static function getFacadeAccessor()
    {
        //TestRedis::class 则不需要在instance()中调用get
        return RedisFactory::class;
    }

    public static function instance()
    {
        return parent::instance()->get('default');
    }

}