<?php

namespace App\Command\Amazon\Catalog;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerInterface;

#[Command]
class SearchCatalogItems extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:catalog:search-catalog-items');
    }

    public function handle()
    {

    }
}