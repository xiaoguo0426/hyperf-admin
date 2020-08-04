<?php


namespace App\Factory;


use Hyperf\Redis\Redis;

class TestRedisFactory extends Redis
{
    protected $poolName = 'test';
}