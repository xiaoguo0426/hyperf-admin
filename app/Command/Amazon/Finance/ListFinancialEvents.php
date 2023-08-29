<?php

namespace App\Command\Amazon\Finance;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerInterface;

#[Command]
class ListFinancialEvents extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:finance:list-financial-events');
    }

    public function configure(): void
    {
        parent::configure();
        $this->setDescription('Amazon Finance List Financial Events Command');
    }

    public function handle(): void
    {
    }
}