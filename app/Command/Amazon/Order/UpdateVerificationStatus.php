<?php

namespace App\Command\Amazon\Order;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerInterface;

#[Command]
class UpdateVerificationStatus extends HyperfCommand
{

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:order:update-verification-status');
    }

    public function handle()
    {

    }
}