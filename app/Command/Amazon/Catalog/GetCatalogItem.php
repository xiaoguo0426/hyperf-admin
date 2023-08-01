<?php

namespace App\Command\Amazon\Catalog;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerInterface;

#[Command]
class GetCatalogItem extends HyperfCommand
{

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:catalog:get-catalog-items');
    }

    public function handle()
    {
        // TODO: Implement handle() method.
    }
}