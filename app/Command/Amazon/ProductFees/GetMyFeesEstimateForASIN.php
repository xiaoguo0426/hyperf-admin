<?php

namespace App\Command\Amazon\ProductFees;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerInterface;

#[Command]
class GetMyFeesEstimateForASIN extends HyperfCommand
{

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:product-fees:get-my-fees-estimate-for-asin');
    }

    public function handle()
    {

    }
}