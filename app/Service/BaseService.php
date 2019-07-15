<?php


namespace App\Service;

use Hyperf\Redis\RedisFactory;
use Psr\Container\ContainerInterface;
use Hyperf\Utils\ApplicationContext;

class BaseService
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct()
    {
        $this->container = ApplicationContext::getContainer();
    }

    public function getRedis()
    {
        return $this->container->get(RedisFactory::class)->get('default');
    }

}