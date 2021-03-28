<?php


namespace App\Factory;


use Hyperf\Redis\Redis;

class DefaultRedis extends Redis
{
    protected $poolName = 'default';
}