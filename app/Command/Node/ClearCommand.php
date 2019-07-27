<?php

declare(strict_types=1);

namespace App\Command\Node;

use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerInterface;
use Hyperf\Command\Annotation\Command;
use Symfony\Component\Console\Input\InputArgument;

/**
 * @Command
 */
class ClearCommand extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('node:clear');
    }

    public function configure()
    {
        $this->setDescription('Hyperf Node Clear Command');
    }

    public function handle()
    {


    }

}

