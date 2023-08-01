<?php

namespace App\Command\Amazon\Feeds;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerInterface;

#[Command]
class CancelFeed extends HyperfCommand
{

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:feeds:cancel-feed');
    }

    public function handle()
    {
        // TODO: Implement handle() method.
    }
}