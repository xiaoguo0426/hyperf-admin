<?php

declare(strict_types=1);

namespace App\Command\Amazon;

use AmazonPHP\SellingPartner\AccessToken;
use AmazonPHP\SellingPartner\SellingPartnerSDK;
use App\Queue\AmazonActionReportQueue;
use App\Util\Amazon\Report\ReportBase;
use App\Util\Amazon\Report\ReportFactory;
use App\Util\AmazonApp;
use App\Util\AmazonSDK;
use App\Util\Log\AmazonReportActionLog;
use Carbon\Carbon;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RedisException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[Command]
class ReportAction extends HyperfCommand
{
    #[Inject]
    private AmazonReportActionLog $amazonReportLog;

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:report-action');
    }

    public function configure(): void
    {
        parent::configure();
        $this->setDescription('Amazon Action Report');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     */
    public function handle(): void
    {
        (new AmazonActionReportQueue())->pop();
    }

}
