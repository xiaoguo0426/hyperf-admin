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
use AmazonPHP\SellingPartner\Model\Reports\ReportDocument;
use AmazonPHP\SellingPartner\SellingPartnerSDK;
use App\Queue\Data\AmazonGetReportDocumentData;
use App\Queue\Data\AmazonReportDocumentActionData;
use App\Queue\Data\QueueDataInterface;
use App\Util\AmazonApp;
use App\Util\AmazonSDK;
use App\Util\Log\AmazonReportDocumentLog;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Client\ClientExceptionInterface;

class AmazonGetReportDocumentQueue extends Queue
{
    public function getQueueName(): string
    {
        return 'amazon-get-report-document';
    }

    public function getQueueDataClass(): string
    {
        return AmazonGetReportDocumentData::class;
    }

    /**
     * @param QueueDataInterface $queueData
     * @throws ApiException
     * @throws ClientExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \JsonException
     * @return bool
     */
    public function handleQueueData(QueueDataInterface $queueData): bool
    {
        /**
         * @var AmazonGetReportDocumentData $queueData
         */
        $merchant_id = $queueData->getMerchantId();
        $merchant_store_id = $queueData->getMerchantStoreId();
        $marketplace_ids = $queueData->getMarketplaceIds();
        $report_type = $queueData->getReportType();
        $report_document_id = $queueData->getReportDocumentId();

        $logger = ApplicationContext::getContainer()->get(AmazonReportDocumentLog::class);
        $logger->info(sprintf('Get Document 报告队列数据：%s', $queueData->toJson()));

        return AmazonApp::tok($merchant_id, $merchant_store_id, static function (AmazonSDK $amazonSDK, int $merchant_id, int $merchant_store_id, SellingPartnerSDK $sdk, AccessToken $accessToken, string $region, array $marketplace_ids) use ($report_type, $report_document_id) {
            $amazonReportDocumentActionQueue = new AmazonReportDocumentActionQueue();

            $console = ApplicationContext::getContainer()->get(StdoutLoggerInterface::class);

            $logger = ApplicationContext::getContainer()->get(AmazonReportDocumentLog::class);

            $retry = 10;

            $dir = sprintf('%s%s/%s/%s-%s/', config('amazon.report_template_path'), 'scheduled', $report_type, $merchant_id, $merchant_store_id);
            $file_base_name = $report_document_id;

            while (true) {
                try {
                    $response = $sdk->reports()->getReportDocument($accessToken, $region, $report_document_id);

                    $document_url = $response->getUrl();
                    $file_path = $dir . $file_base_name . '.txt';

                    $log = sprintf('Get Document 报告生成成功 report_type: %s  report_document_id: %s  url: %s merchant_id: %s merchant_store_id: %s', $report_type, $report_document_id, $document_url, $merchant_id, $merchant_store_id);
                    $logger->info($log);
                    $console->info($log);

                    $compression_algorithm = $response->getCompressionAlgorithm();

                    if ($compression_algorithm === ReportDocument::COMPRESSION_ALGORITHM_GZIP) {
                        $file_path_gz = $dir . $file_base_name . '.gz';
                        file_put_contents($file_path_gz, file_get_contents($document_url)); // 保存gz文件

                        $handle = fopen($file_path, 'wb');

                        $buffer_size = 4096; // read 4kb at a time
                        $handle_gz = gzopen($file_path_gz, 'rb');

                        while (! gzeof($handle_gz)) {
                            fwrite($handle, gzread($handle_gz, $buffer_size)); // 提取gz文件内容
                        }

                        gzclose($handle_gz);
                        fclose($handle);
                        // 线上环境gz文件解压提取后需要删除
                        //                        if (! app()->isDebug()) {
                        //                        unlink($file_path_gz);
                        //                        }
                    } else {
                        // 下载并保存文件
                        file_put_contents($file_path, file_get_contents($document_url));
                    }

                    $amazonReportDocumentActionData = new AmazonReportDocumentActionData();
                    $amazonReportDocumentActionData->setMerchantId($merchant_id);
                    $amazonReportDocumentActionData->setMerchantStoreId($merchant_store_id);
                    $amazonReportDocumentActionData->setReportType($report_type);
                    $amazonReportDocumentActionData->setReportDocumentId($report_document_id);

                    $amazonReportDocumentActionQueue->push($amazonReportDocumentActionData);

                    sleep(5);

                    return true;
                } catch (ApiException $e) {
                    --$retry;
                    if ($retry > 0) {
                        $console->warning(sprintf('Get Document report_type: %s report_document_id: %s retry: %s ', $report_type, $report_document_id, $retry));
                        sleep(10);
                        continue;
                    }

                    $log = sprintf('Get Document report_type: %s  report_document_id: %s merchant_id: %s merchant_store_id: %s 获取报告出错 %s', $report_type, $report_document_id, $merchant_id, $merchant_store_id, json_encode([
                        'merchant_id' => $merchant_id,
                        'merchant_store_id' => $merchant_store_id,
                        'marketplace_ids' => $marketplace_ids,
                        'report_document_id' => $report_document_id,
                        'report_type' => $report_type,
                    ], JSON_THROW_ON_ERROR));

                    $console->error($log);
                    $logger->error($log, [
                        'message' => $e->getMessage(),
                        'response body' => $e->getResponseBody(),
                    ]);

                    break;
                } catch (InvalidArgumentException $e) {
                    $logger->error(sprintf('Get Document report_type: %s  report_id: %s merchant_id: %s merchant_store_id: %s 获取报告出错', $report_type, $report_document_id, $merchant_id, $merchant_store_id), [
                        'message' => 'InvalidArgumentException ' . $e->getMessage(),
                    ]);
                    break;
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
