<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Queue;

use AmazonPHP\SellingPartner\AccessToken;
use AmazonPHP\SellingPartner\Exception\ApiException;
use AmazonPHP\SellingPartner\Exception\InvalidArgumentException;
use AmazonPHP\SellingPartner\Model\Reports\Report;
use AmazonPHP\SellingPartner\Model\Reports\ReportDocument;
use AmazonPHP\SellingPartner\SellingPartnerSDK;
use App\Queue\Data\AmazonActionReportData;
use App\Queue\Data\AmazonGetReportData;
use App\Queue\Data\QueueDataInterface;
use App\Util\Amazon\Report\ReportFactory;
use App\Util\AmazonApp;
use App\Util\AmazonSDK;
use App\Util\Log\AmazonReportGetLog;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Client\ClientExceptionInterface;

class AmazonGetReportQueue extends Queue
{
    #[Inject]
    private AmazonReportGetLog $amazonReportLog;

    public function getQueueName(): string
    {
        return 'amazon-get-report';
    }

    public function getQueueDataClass(): string
    {
        return AmazonGetReportData::class;
    }

    /**
     * @throws \Exception
     * @throws ClientExceptionInterface
     */
    public function handleQueueData(QueueDataInterface $queueData): bool
    {
        /**
         * @var AmazonGetReportData $queueData
         */
        $merchant_id = $queueData->getMerchantId();
        $merchant_store_id = $queueData->getMerchantStoreId();
        $marketplace_ids = $queueData->getMarketplaceIds();
        $report_type = $queueData->getReportType();
        $report_id = $queueData->getReportId();
        $start_time = $queueData->getDataStartTime();
        $end_time = $queueData->getDataEndTime();

        $logger = $this->amazonReportLog;

        $logger->info(sprintf('Get 报告队列数据： %s', $queueData->toJson()));

        return AmazonApp::tok($merchant_id, $merchant_store_id, static function (AmazonSDK $amazonSDK, int $merchant_id, int $merchant_store_id, SellingPartnerSDK $sdk, AccessToken $accessToken, string $region, array $marketplace_ids) use ($report_type, $report_id, $start_time, $end_time, $logger) {
            $queue = new AmazonActionReportQueue();

            $console = ApplicationContext::getContainer()->get(StdoutLoggerInterface::class);

            $retry = 10;

            while (true) {
                try {
                    $response = $sdk->reports()->getReport($accessToken, $region, $report_id);

                    $marketplace_ids = $response->getMarketplaceIds() ?: [];
                    $report_id = $response->getReportId();
                    //                    $report_type = $response->getReportType();
                    $dataStartTime = $response->getDataStartTime();
                    $dataEndTime = $response->getDataEndTime();
                    $report_schedule_id = $response->getReportScheduleId() ?: '';
                    $create_time = $response->getCreatedTime();
                    $processing_status = $response->getProcessingStatus(); // 要判断是否为DONE   CANCELLED报告被取消,DONE报告已完成处理,FATAL报告因致命错误而中止,IN_PROGRESS该报告正在处理中,IN_QUEUE该报告尚未开始处理
                    $processing_start_time = $response->getProcessingStartTime();
                    $processing_end_time = $response->getProcessingEndTime();
                    $report_document_id = $response->getReportDocumentId() ?: '';

                    if ($processing_status === Report::PROCESSING_STATUS_IN_PROGRESS || $processing_status === Report::PROCESSING_STATUS_IN_QUEUE) {
                        // 重新入队
                        $logger->notice(sprintf('Get %s  %s 报告处理中 merchant_id: %s merchant_store_id: %s', $report_type, $report_id, $merchant_id, $merchant_store_id));
                        return false;
                    }
                    if ($processing_status === Report::PROCESSING_STATUS_CANCELLED || $processing_status === Report::PROCESSING_STATUS_FATAL) {
                        // 被取消和终止的，不处理
                        $log = sprintf('Get %s  %s 报告被取消或出错 %s merchant_id: %s merchant_store_id: %s', $report_type, $report_id, $processing_status, $merchant_id, $merchant_store_id);
                        $logger->error($log);
                        $console->error($log);
                        return true;
                    }

                    $document = $sdk->reports()->getReportDocument($accessToken, $region, $report_document_id);
                    $url = $document->getUrl();

                    $log = sprintf('Get 报告生成成功 report_type: %s  report_id: %s  url: %s merchant_id: %s merchant_store_id: %s', $report_type, $report_id, $url, $merchant_id, $merchant_store_id);
                    $logger->info($log);
                    $console->info($log);

                    $instance = ReportFactory::getInstance($merchant_id, $merchant_store_id, $report_type);
                    $instance->setReportStartDate($start_time);
                    $instance->setReportEndDate($end_time);

                    if ($instance->checkDir() === false) {
                        $console->error('报告保存路径有误，请检查 ' . $instance->getDir());
                        return true;
                    }
                    $dir = $instance->getDir();

                    $file_path = $instance->getReportFilePath($marketplace_ids);

                    $compression_algorithm = $document->getCompressionAlgorithm();
                    if ($compression_algorithm === ReportDocument::COMPRESSION_ALGORITHM_GZIP) {
                        $file_base_name = $instance->getReportFileName($marketplace_ids);

                        $file_path_gz = $dir . $file_base_name . '.gz';
                        file_put_contents($file_path_gz, file_get_contents($url)); // 保存gz文件

                        $handle = fopen($file_path, 'wb');

                        $buffer_size = 4096; // read 4kb at a time
                        $handle_gz = gzopen($file_path_gz, 'rb');

                        while (! gzeof($handle_gz)) {
                            fwrite($handle, gzread($handle_gz, $buffer_size));
                        }

                        gzclose($handle_gz);
                        fclose($handle);
                        // 线上环境gz文件解压提取后需要删除
                        if (\Hyperf\Support\env('APP_ENV') !== 'dev') {
                            unlink($file_path_gz);
                        }
                    } else {
                        // 下载并保存文件
                        file_put_contents($file_path, file_get_contents($url));
                    }

                    $console->info(sprintf('文件保存路径 %s', $file_path));

                    // 将报告保存的路径投递到队列中
                    $queueData = new AmazonActionReportData();
                    $queueData->setMerchantId($merchant_id);
                    $queueData->setMerchantStoreId($merchant_store_id);
                    $queueData->setMarketplaceIds($marketplace_ids);
                    $queueData->setReportId($report_id);
                    $queueData->setReportType($report_type);
                    $queueData->setReportFilePath($file_path);
                    $queueData->setDataStartTime($dataStartTime?->format('Y-m-d H:i:s'));
                    $queueData->setDataEndTime($dataEndTime?->format('Y-m-d H:i:s'));

                    $queue->push($queueData);

                    sleep(5);

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

    public function safetyLine(): int
    {
        return 70;
    }
}
