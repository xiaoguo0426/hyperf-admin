<?php

declare(strict_types=1);

namespace App\Command\Amazon;

use AmazonPHP\SellingPartner\AccessToken;
use AmazonPHP\SellingPartner\Exception\ApiException;
use AmazonPHP\SellingPartner\Exception\InvalidArgumentException;
use AmazonPHP\SellingPartner\Model\Reports\CreateReportSpecification;
use AmazonPHP\SellingPartner\SellingPartnerSDK;
use App\Queue\AmazonGetReportQueue;
use App\Queue\Data\AmazonGetReportData;
use App\Util\Amazon\Report\ReportBase;
use App\Util\Amazon\Report\ReportFactory;
use App\Util\AmazonApp;
use App\Util\AmazonSDK;
use App\Util\Log\AmazonReportCreateLog;
use Carbon\Carbon;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[Command]
class ReportCreate extends HyperfCommand
{

    #[Inject]
    private AmazonReportCreateLog $amazonReportCreateLog;

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:report-create');
    }

    public function configure(): void
    {
        parent::configure();
        // 指令配置
        $this->addArgument('merchant_id', InputArgument::REQUIRED, '商户id')
            ->addArgument('merchant_store_id', InputArgument::REQUIRED, '店铺id')
            ->addArgument('report_type', InputArgument::REQUIRED, '报告类型')
            ->addOption('report_start_date', null, InputOption::VALUE_REQUIRED, '报告开始日期', null)
            ->addOption('report_end_date', null, InputOption::VALUE_REQUIRED, '报告结束日期', null)
            ->addOption('is_range_date', null, InputOption::VALUE_REQUIRED, '报告日期是否为范围', '1')
            ->setDescription('Amazon Create Report');
    }

    public function handle(): void
    {

        $merchant_id = (int) $this->input->getArgument('merchant_id');
        $merchant_store_id = (int) $this->input->getArgument('merchant_store_id');
        $report_type = (string) $this->input->getArgument('report_type');
        $report_start_date = $this->input->getOption('report_start_date');
        $report_end_date = $this->input->getOption('report_end_date');
        $is_range_date = (string) $this->input->getOption('is_range_date');

        if ($report_start_date) {
            $reportStartDate = new Carbon($report_start_date);
            $report_start_date = $reportStartDate->format('Y-m-d');
        }
        if ($report_end_date) {
            $reportEndDate = new Carbon($report_end_date);
            $report_end_date = $reportEndDate->format('Y-m-d');
        }

        if ($is_range_date !== '1') {
            $this->fly($merchant_id, $merchant_store_id, $report_type, $report_start_date, $report_end_date);
        } else {
            $date_ranges = [];

            $reportStartDate = new Carbon($report_start_date);
            $reportEndDate = new Carbon($report_end_date);

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
        $logger = $this->amazonReportCreateLog;
        AmazonApp::tok($merchant_id, $merchant_store_id, static function (AmazonSDK $amazonSDK, int $merchant_id, int $merchant_store_id, string $seller_id, SellingPartnerSDK $sdk, AccessToken $accessToken, string $region, array $marketplace_ids) use ($report_type, $report_start_date, $report_end_date, $logger) {

            $instance = ReportFactory::getInstance($merchant_id, $merchant_store_id, $report_type);

            if (! is_null($report_start_date)) {
                $instance->setReportStartDate($report_start_date);
            }
            if (! is_null($report_end_date)) {
                $instance->setReportEndDate($report_end_date);
            }

            //解决某些Report只能传一个marketplace_id的问题，但同时店铺又存在多个地区。需要重写requestReport方法，参考GET_SALES_AND_TRAFFIC_REPORT
            $instance->requestReport($marketplace_ids, function (ReportBase $instance, $report_type, CreateReportSpecification $body, array $marketplace_ids) use ($sdk, $accessToken, $region, $logger, $merchant_id, $merchant_store_id, $seller_id) {
                $console = ApplicationContext::getContainer()->get(StdoutLoggerInterface::class);

                //注意匿名函数里的$marketplace_ids的值
                if (false === $instance->checkDir()) {
                    $console->error('报告保存路径有误，请检查 ' . $instance->getDir());
                    return true;
                }
                $dir = $instance->getDir();

                if ($instance->checkReportFile($marketplace_ids)) {
                    //文件存在了直接返回
                    $console->warning($instance->getReportFilePath($marketplace_ids) . ' 文件已存在');
                    return true;
                }

                $retry = 10;

                $data_start_time = $body->getDataStartTime() ? $body->getDataStartTime()->format('Y-m-d H:i:s') : '';
                $data_end_time = $body->getDataEndTime() ? $body->getDataEndTime()->format('Y-m-d H:i:s') : '';

                $queue = new AmazonGetReportQueue();

                while (true) {
                    try {

                        $response = $sdk->reports()->createReport($accessToken, $region, $body);
                        $report_id = $response->getReportId();

                        $amazonGetReportData = \Hyperf\Support\make(AmazonGetReportData::class);
                        $amazonGetReportData->setMerchantId($merchant_id);
                        $amazonGetReportData->setMerchantStoreId($merchant_store_id);
                        $amazonGetReportData->setMarketplaceIds($marketplace_ids);
                        $amazonGetReportData->setReportId($report_id);
                        $amazonGetReportData->setReportType($report_type);
                        $amazonGetReportData->setDataStartTime($data_start_time);
                        $amazonGetReportData->setDataEndTime($data_end_time);

                        $log = sprintf('Create %s report_id: %s merchant_id: %s merchant_store_id: %s  start_time:%s end_time:%s', $report_type, $report_id, $merchant_id, $merchant_store_id, $data_start_time, $data_end_time);
                        $logger->info($log);
                        $logger->info($log, [
                            'marketplace_ids' => $marketplace_ids,
                            'data_start_time' => $data_start_time,
                            'data_end_time' => $data_end_time
                        ]);

                        $queue->push($amazonGetReportData);

                        break;
                    } catch (ApiException $e) {
                        $retry--;
                        if ($retry > 0) {
                            $console->warning(sprintf('Create %s report fail, retry: %s  merchant_id: %s merchant_store_id: %s ', $report_type, $retry, $merchant_id, $merchant_store_id));
                            sleep(10);
                            continue;
                        }
                        $logger->error(sprintf('ApiException %s 创建报告出错 merchant_id: %s merchant_store_id: %s', $report_type, $merchant_id, $merchant_store_id), [
                            'message' => $e->getMessage(),
                            'response body' => $e->getResponseBody(),
                            'data_start_time' => $body->getDataStartTime(),
                            'data_end_time' => $body->getDataEndTime()
                        ]);
                        break;
                    } catch (InvalidArgumentException $e) {
                        $logger->error(sprintf('InvalidArgumentException %s 创建报告出错 merchant_id: %s merchant_store_id: %s', $report_type, $merchant_id, $merchant_store_id));
                        break;
                    }
                }

                sleep(5);
                return true;
            });

            return true;
        });
    }
}
