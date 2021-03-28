<?php
declare(strict_types=1);

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