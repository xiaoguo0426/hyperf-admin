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

}