<?php

namespace App\Command\Amazon\Feeds;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerInterface;

#[Command]
class CreateFeedDocument extends HyperfCommand
{

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:feeds:create-feed-document');
    }

    public function handle()
    {
        // TODO: Implement handle() method.
    }
}