<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Command\Amazon\Report;

use App\Queue\AmazonActionReportQueue;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RedisException;

#[Command]
class ReportAction extends HyperfCommand
{

    /**
     * @param ContainerInterface $container
     */
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:report:action');
    }

    /**
     * @return void
     */
    public function configure(): void
    {
        parent::configure();
        $this->setDescription('Amazon Action Report');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     * @return void
     */
    public function handle(): void
    {
        (new AmazonActionReportQueue())->pop();
    }
}