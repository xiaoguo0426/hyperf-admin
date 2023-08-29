<?php

namespace App\Command\Amazon\Finance;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerInterface;

#[Command]
class ListFinancialEventsByOrderId extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:finance:list-financial-events-by-order-id');
    }

    public function configure(): void
    {
        parent::configure();
        $this->setDescription('Amazon Finance List Financial Events By Order Id Command');
    }

    public function handle(): void
    {
    }
}