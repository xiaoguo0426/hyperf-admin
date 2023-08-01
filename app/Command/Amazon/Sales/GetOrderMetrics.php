<?php

namespace App\Command\Amazon\Sales;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerInterface;

#[Command]
class GetOrderMetrics extends HyperfCommand
{

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:order:get-order-metrics');
    }

    public function handle()
    {

    }
}