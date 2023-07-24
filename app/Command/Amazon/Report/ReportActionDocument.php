<?php

namespace App\Command\Amazon\Report;

use App\Queue\AmazonReportDocumentActionQueue;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RedisException;

#[Command]
class ReportActionDocument extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:report:action-document');
    }

    public function configure(): void
    {
        parent::configure();
        $this->setDescription('Amazon Action Report Document Command');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     * @return void
     */
    public function handle(): void
    {
        (new AmazonReportDocumentActionQueue())->pop();
    }
}