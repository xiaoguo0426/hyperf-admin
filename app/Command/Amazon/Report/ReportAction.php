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
use App\Util\Log\AmazonReportActionLog;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Di\Annotation\Inject;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

#[Command]
class ReportAction extends HyperfCommand
{
    #[Inject]
    private AmazonReportActionLog $amazonReportLog;

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:report:action');
    }

    public function configure(): void
    {
        parent::configure();
        $this->setDescription('Amazon Action Report');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \RedisException
     */
    public function handle(): void
    {
        (new AmazonActionReportQueue())->pop();
    }
}
