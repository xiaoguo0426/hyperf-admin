<?php


namespace App\Service;

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