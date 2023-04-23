<?php

declare(strict_types=1);

namespace App\Command\Amazon;

use AmazonPHP\SellingPartner\AccessToken;
use AmazonPHP\SellingPartner\SellingPartnerSDK;
use App\Util\AmazonApp;
use App\Util\AmazonSDK;
use App\Util\Log\AmazonReportLog;
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
        $merchant_id = $this->input->getArgument('merchant_id');
        $merchant_store_id = $this->input->getArgument('merchant_store_id');
        $report_type = $this->input->getArgument('report_type');

        $report_start_date = $this->input->getOption('report_start_date');
        $report_end_date = $this->input->getOption('report_end_date');
        $is_range_date = $this->input->getOption('is_range_date');

        if (is_null($is_range_date) || '0' === $is_range_date) {

            $this->fly((int) $merchant_id, (int) $merchant_store_id, (string) $report_type, (string) $report_start_date, (string) $report_end_date);
        } else {

        }

    }

    private function fly(int $merchant_id, int $merchant_store_id, string $report_type, string $report_start_date, string $report_end_date)
    {
        return AmazonApp::tok($merchant_id, $merchant_store_id, static function (AmazonSDK $amazonSDK, int $merchant_id, int $merchant_store_id, string $seller_id, SellingPartnerSDK $sdk, AccessToken $accessToken, string $region, array $marketplace_ids) {
            $logger = di(AmazonReportLog::class);
            $logger->info('123123');

            return true;
        });
    }
}
