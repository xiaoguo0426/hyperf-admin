<?php

declare(strict_types=1);

namespace App\Command\Amazon;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[Command]
class RunReport extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:run-report');
    }

    public function configure(): void
    {
        parent::configure();
        $this->addArgument('merchant_id', InputArgument::REQUIRED, 'merchant_id');
        $this->addArgument('merchant_store_id', InputArgument::REQUIRED, 'merchant_store_id');
        $this->addArgument('report_type', InputArgument::REQUIRED, 'report_type');
        $this->addOption('report_start_date', null, InputOption::VALUE_OPTIONAL, 'report_start_date');
        $this->addOption('report_end_date', null, InputOption::VALUE_OPTIONAL, 'report_end_date');
        $this->addOption('is_range_date', null, InputOption::VALUE_OPTIONAL, 'is_range_date');
        $this->setDescription('Amazon Run Report Command');
    }

    public function handle(): void
    {

    }
}
