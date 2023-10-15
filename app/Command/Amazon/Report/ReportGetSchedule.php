<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Command\Amazon\Report;

use AmazonPHP\SellingPartner\AccessToken;
use AmazonPHP\SellingPartner\Exception\ApiException;
use AmazonPHP\SellingPartner\Exception\InvalidArgumentException;
use AmazonPHP\SellingPartner\Model\Reports\CreateReportSpecification;
use AmazonPHP\SellingPartner\Model\Reports\Report;
use AmazonPHP\SellingPartner\Model\Reports\ReportDocument;
use AmazonPHP\SellingPartner\SellingPartnerSDK;
use App\Queue\AmazonActionReportQueue;
use App\Queue\AmazonGetReportQueue;
use App\Queue\Data\AmazonActionReportData;
use App\Queue\Data\AmazonGetReportData;
use App\Util\Amazon\Report\ReportBase;
use App\Util\Amazon\Report\ReportFactory;
use App\Util\AmazonApp;
use App\Util\AmazonSDK;
use App\Util\Log\AmazonReportCreateLog;
use Carbon\Carbon;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use JsonException;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[Command]
class ReportGetSchedule extends HyperfCommand
{
    /**
     * @param ContainerInterface $container
     */
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:report:get-schedules');
    }

    /**
     * @return void
     */
    public function configure(): void
    {
        parent::configure();
        // 指令配置
        $this->addArgument('merchant_id', InputArgument::REQUIRED, '商户id')
            ->addArgument('merchant_store_id', InputArgument::REQUIRED, '店铺id')
            ->setDescription('Amazon Report Get Schedule');
    }

    public function handle(): void
    {
        $merchant_id = (int) $this->input->getArgument('merchant_id');
        $merchant_store_id = (int) $this->input->getArgument('merchant_store_id');

        AmazonApp::tok($merchant_id, $merchant_store_id, static function (AmazonSDK $amazonSDK, int $merchant_id, int $merchant_store_id, SellingPartnerSDK $sdk, AccessToken $accessToken, string $region, array $marketplace_ids) {

            $console = ApplicationContext::getContainer()->get(StdoutLoggerInterface::class);

            $retry = 10;

            $schedule_report_type_list = \Hyperf\Config\config('amazon_reports.scheduled');

            while (true) {
                try {
                    $response = $sdk->reports()->getReportSchedules($accessToken, $region, $schedule_report_type_list);
                    $reportSchedules = $response->getReportSchedules();

                    foreach ($reportSchedules as $reportSchedule) {
                        $report_schedule_id = $reportSchedule->getReportScheduleId();
                        $report_type = $reportSchedule->getReportType();
                        $marketplace_ids = $reportSchedule->getMarketplaceIds();
                        $report_options = $reportSchedule->getReportOptions();

                        var_dump($report_type);
                        var_dump($marketplace_ids);
                        var_dump($report_options);
                    }
                    return true;
                } catch (ApiException $e) {
                    --$retry;
                    if ($retry > 0) {
                        $console->warning(sprintf('report_type: %s report_id: %s start_time: %s end_time: %s retry: %s ', $report_type, $report_id, $start_time, $end_time, $retry));
                        sleep(10);
                        continue;
                    }

                    $log = sprintf('Get report_type: %s  report_id: %s merchant_id: %s merchant_store_id: %s 获取报告出错 %s', $report_type, $report_id, $merchant_id, $merchant_store_id, json_encode([
                        'merchant_id' => $merchant_id,
                        'merchant_store_id' => $merchant_store_id,
                        'marketplace_ids' => $marketplace_ids,
                        'report_id' => $report_id,
                        'report_type' => $report_type,
                        'data_start_time' => $start_time,
                        'data_end_time' => $end_time,
                    ], JSON_THROW_ON_ERROR));

                    $console->error($log);
                    $logger->error($log, [
                        'message' => $e->getMessage(),
                        'response body' => $e->getResponseBody(),
                    ]);

                    break;
                } catch (InvalidArgumentException $e) {
                    $logger->error(sprintf('Get report_type: %s  report_id: %s merchant_id: %s merchant_store_id: %s 获取报告出错', $report_type, $report_id, $merchant_id, $merchant_store_id), [
                        'message' => 'InvalidArgumentException ' . $e->getMessage(),
                    ]);
                    break;
                } catch (\ErrorException $errorException) {
                    $logger->error(sprintf('Get report_type: %s  report_id: %s merchant_id: %s merchant_store_id: %s 获取报告出错', $report_type, $report_id, $merchant_id, $merchant_store_id), [
                        'message' => 'ErrorException ' . $errorException->getMessage(),
                    ]);
                }
            }

            return true;
        });
    }

}
