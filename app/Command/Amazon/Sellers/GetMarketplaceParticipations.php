<?php

namespace App\Command\Amazon\Sellers;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerInterface;

#[Command]
class GetMarketplaceParticipations extends HyperfCommand
{

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:sellers:get-marketplace-participations');
    }

    public function handle()
    {

    }
}