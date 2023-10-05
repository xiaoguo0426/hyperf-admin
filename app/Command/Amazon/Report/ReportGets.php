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
use AmazonPHP\SellingPartner\SellingPartnerSDK;
use App\Queue\AmazonGetReportDocumentQueue;
use App\Queue\Data\AmazonGetReportDocumentData;
use App\Util\AmazonApp;
use App\Util\AmazonSDK;
use App\Util\Log\AmazonReportLog;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

#[Command]
class ReportGets extends HyperfCommand
{
    /**
     * @param ContainerInterface $container
     */
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:report:gets');
    }

    /**
     * @return void
     */
    public function configure(): void
    {
        parent::configure();
        $this->setDescription('Amazon Gets Report Command');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @return void
     */
    public function handle(): void
    {
        AmazonApp::process(static function (AmazonSDK $amazonSDK, int $merchant_id, int $merchant_store_id, SellingPartnerSDK $sdk, AccessToken $accessToken, string $region, array $marketplace_ids) {
            $logger = ApplicationContext::getContainer()->get(AmazonReportLog::class);
            $console = ApplicationContext::getContainer()->get(StdoutLoggerInterface::class);

            $report_types = \Hyperf\Config\config('amazon_reports.scheduled');
            if (empty($report_types)) {
                $logger->error('请配置亚马逊报告');
                return false;
            }
            $process_states = ['DONE']; // 报告处理进度
            $page_size = 100; // 分页大小
            $created_since = null; // 默认获取最近90天的数据
            $created_until = null;
            $next_token = null;

            $report_template_path = \Hyperf\Config\config('amazon.report_template_path');
            $queue = new AmazonGetReportDocumentQueue();

            foreach ($report_types as $report_type) {
                $report_document_ids = [];

                $retry = 10;
                while (true) {
                    try {
                        $response = $sdk->reports()->getReports($accessToken, $region, [$report_type], $process_states, $marketplace_ids, $page_size, $created_since, $created_until, $next_token);

                        $reports = $response->getReports();

                        foreach ($reports as $report) {
                            $report_type = $report->getReportType();
//                            $marketplace_ids = $report->getMarketplaceIds();
                            $data_start_time = $report->getDataStartTime();
                            $data_end_time = $report->getDataEndTime();
                            $report_schedule_id = $report->getReportScheduleId();
                            $report_document_id = $report->getReportDocumentId();

                            $dir = sprintf('%s%s/%s/%s-%s/', $report_template_path, 'scheduled', $report_type, $merchant_id, $merchant_store_id);
                            if (! is_dir($dir) && ! mkdir($dir, 0755, true)) {
                                $logger->error(sprintf('Get Directory "%s" was not created', $dir));
                                break;
                            }
                            $file_base_name = $report_document_id;
                            $file_path = $dir . $file_base_name . '.txt';
                            if (file_exists($file_path)) {
                                // 文件存在了直接返回
                                $console->warning($file_path . ' 文件已存在');
                                //                                continue;
                            }

                            $amazonGetReportDocumentData = new AmazonGetReportDocumentData();
                            $amazonGetReportDocumentData->setMerchantId($merchant_id);
                            $amazonGetReportDocumentData->setMerchantStoreId($merchant_store_id);
                            $amazonGetReportDocumentData->setReportDocumentId($report_document_id);
                            $amazonGetReportDocumentData->setReportType($report_type);
                            $amazonGetReportDocumentData->setMarketplaceIds($marketplace_ids);

                            // 将同一报告类型 的文档id投递到队列，异步拉取报告
                            $queue->push($amazonGetReportDocumentData);
                        }

                        $next_token = $response->getNextToken();
                        if (is_null($next_token)) {
                            break;
                        }
                    } catch (ApiException $e) {
                        --$retry;
                        if ($retry > 0) {
                            $console->warning(sprintf('Report Gets. report_type: %s retry: %s ', $report_type, $retry));
                            sleep(10);
                            continue;
                        }

                        $log = sprintf('Report Gets report_type: %s merchant_id: %s merchant_store_id: %s 获取报告出错 %s', $report_type, $merchant_id, $merchant_store_id, json_encode([
                            'merchant_id' => $merchant_id,
                            'merchant_store_id' => $merchant_store_id,
                            'marketplace_ids' => $marketplace_ids,
                            'report_type' => $report_type,
                        ], JSON_THROW_ON_ERROR));

                        $console->error($log);
                        $logger->error($log, [
                            'message' => $e->getMessage(),
                            'response body' => $e->getResponseBody(),
                        ]);
                    } catch (InvalidArgumentException $e) {
                        $logger->error(sprintf('InvalidArgumentException %s 创建报告出错 merchant_id: %s merchant_store_id: %s', $report_type, $merchant_id, $merchant_store_id));
                        break;
                    }
                }
            }

            return true;
        });
    }
}
