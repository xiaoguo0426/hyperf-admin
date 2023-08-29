<?php

namespace App\Command\Amazon\Finance;

use App\Queue\AmazonFinanceFinancialListEventsByGroupIdQueue;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RedisException;

#[Command]
class ListFinancialEventsByGroupId extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:finance:list-financial-events-by-group-id');
    }

    public function configure(): void
    {
        parent::configure();
        $this->setDescription('Amazon Finance List Financial Events By Group Id Command');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     */
    public function handle(): void
    {
        (new AmazonFinanceFinancialListEventsByGroupIdQueue())->pop();
    }
}