<?php

declare(strict_types=1);

namespace App\Command\Amazon;

use App\Queue\AmazonGetReportQueue;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RedisException;

#[Command]
class ReportGet extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:report-get');
    }

    public function configure(): void
    {
        parent::configure();
        $this->setDescription('Amazon Get Report Command');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     * @return void
     */
    public function handle(): void
    {
        (new AmazonGetReportQueue())->pop();
    }
}
