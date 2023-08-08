<?php

namespace App\Command\Amazon\Replenishment;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerInterface;

#[Command]
class ListOfferMetrics extends HyperfCommand
{

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:replenishment:list-offer-metrics');
    }

    public function handle()
    {

    }
}