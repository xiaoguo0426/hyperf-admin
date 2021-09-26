<?php

declare(strict_types=1);

namespace App\Command\Node;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerInterface;

/**
 * @Command
 */
class RefreshCommand extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('node:refresh');
    }

    public function configure(): void
    {
        $this->setDescription('Hyperf Nodes Refresh Command');
    }

    public function handle(): void
    {
        $nodes_path = config('nodes_path');
        file_exists($nodes_path) && unlink($nodes_path);

        $this->call('node:create');

        $this->comment('Nodes Data has successfully Refreshed!');
    }
}
