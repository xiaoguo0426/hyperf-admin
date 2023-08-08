<?php

namespace App\Command\Amazon\Replenishment;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerInterface;

#[Command]
class ListOffers extends HyperfCommand
{

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:replenishment:list-offers');
    }

    public function handle()
    {

    }
}