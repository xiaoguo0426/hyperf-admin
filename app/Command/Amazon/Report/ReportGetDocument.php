<?php

declare(strict_types=1);

namespace App\Command\Amazon\Report;

use App\Queue\AmazonGetReportDocumentQueue;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RedisException;

#[Command]
class ReportGetDocument extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:report:gets-document');
    }

    public function configure(): void
    {
        parent::configure();
        $this->setDescription('Amazon Gets Report Document Command');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     */
    public function handle(): void
    {
        (new AmazonGetReportDocumentQueue)->pop();
    }
}