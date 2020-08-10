<?php


namespace App\Factory;


use Hyperf\Redis\Redis;

class TestRedis extends Redis
{
    protected $poolName = 'test';
}