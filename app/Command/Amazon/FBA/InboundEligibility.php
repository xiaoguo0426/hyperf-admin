<?php

namespace App\Command\Amazon\FBA;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerInterface;

#[Command]
class InboundEligibility extends HyperfCommand
{

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:fba:inbound-eligibility');
    }

    public function handle()
    {
    }
}