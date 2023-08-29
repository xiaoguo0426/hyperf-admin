<?php

namespace App\Command\Amazon\FulfillmentInbound;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerInterface;

#[Command]
class PutTransportDetails extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:fulfillment-inbound:get-inbound-guidance');
    }

    public function configure(): void
    {
        parent::configure();
        $this->setDescription('Amazon Fulfillment Inbound Get Inbound Guidance Command');
    }

    public function handle(): void
    {
    }
}