<?php

declare(strict_types=1);

namespace App\Service;

use Hyperf\Utils\ApplicationContext;
use Psr\Container\ContainerInterface;

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
