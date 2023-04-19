<?php

declare(strict_types=1);

namespace App\Command\Amazon;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;

#[Command]
class CreateReport extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:create-report');
    }

    public function configure(): void
    {
        parent::configure();
        $this->setDescription('Amazon Create Report Command');
    }

    public function handle(): void
    {
        $this->line('Hello Hyperf!', 'info');
    }
}
