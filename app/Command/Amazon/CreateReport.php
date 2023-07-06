<?php

declare(strict_types=1);

namespace App\Command\Amazon;

use AmazonPHP\SellingPartner\AccessToken;
use AmazonPHP\SellingPartner\SellingPartnerSDK;
use App\Util\AmazonApp;
use App\Util\AmazonSDK;
use Carbon\Carbon;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[Command]
class CreateReport extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:create-report');
    }

    public function configure(): void
    {
        parent::configure();
        // 指令配置
        $this->setName('fake:report-create')
            ->addArgument('merchant_id', InputArgument::REQUIRED, '商户id')
            ->addArgument('merchant_store_id', InputArgument::REQUIRED, '店铺id')
            ->addArgument('report_type', InputArgument::REQUIRED, '报告类型')
            ->addOption('report_start_date', null, InputOption::VALUE_REQUIRED, '报告开始日期', '')
            ->addOption('report_end_date', null, InputOption::VALUE_REQUIRED, '报告结束日期', '')
            ->addOption('is_range_date', null, InputOption::VALUE_REQUIRED, '报告日期是否为范围', '1')
            ->setDescription('fake Create Report');
    }

    public function handle(): void
    {

        $merchant_id = $this->input->getArgument('merchant_id');
        $merchant_store_id = $this->input->getArgument('merchant_store_id');
        $report_type = $this->input->getArgument('report_type');
        $report_start_date = $this->input->getOption('report_start_date');
        $report_end_date = $this->input->getOption('report_end_date');
        $is_range_date = $this->input->getOption('is_range_date');

        var_dump($merchant_id);
        var_dump($merchant_store_id);
        var_dump($report_type);
        var_dump($report_start_date);
        var_dump($report_end_date);
        var_dump($is_range_date);

        $reportStartDate = new Carbon($report_start_date);
        $report_start_date = $reportStartDate->format('Y-m-d');
        $reportEndDate = new Carbon($report_end_date);
        $report_end_date = $reportEndDate->format('Y-m-d');

        if ($is_range_date !== '1') {
            $this->fly($merchant_id, $merchant_store_id, $report_type, $report_start_date, $report_end_date);
        } else {
            $date_ranges = [];

            $diff_days = $reportEndDate->diffInDays($reportStartDate) + 1;

            while ($diff_days > 0) {
                $date_ranges[] = [
                    'start_date' => $reportStartDate->format('Y-m-d 00:00:00'),
                    'end_date' => $reportStartDate->format('Y-m-d 23:59:59'),
                ];

                $reportStartDate->addDay();

                $diff_days--;
            }

            foreach ($date_ranges as $date_range) {
                $this->fly($merchant_id, $merchant_store_id, $report_type, $date_range['start_date'], $date_range['end_date']);
            }
        }
    }

    private function fly($merchant_id, $merchant_store_id, $report_type, $report_start_date, $report_end_date): void
    {
        AmazonApp::tok($merchant_id, $merchant_store_id, static function (AmazonSDK $amazonSDK, int $merchant_id, int $merchant_store_id, string $seller_id, SellingPartnerSDK $sdk, AccessToken $accessToken, string $region, array $marketplace_ids) use ($report_type, $report_start_date, $report_end_date) {



        });
    }
}
