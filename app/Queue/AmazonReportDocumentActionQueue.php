<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Queue;

use App\Queue\Data\AmazonReportDocumentActionData;
use App\Queue\Data\QueueDataInterface;
use App\Util\Amazon\Report\ReportFactory;
use App\Util\Log\AmazonReportDocumentLog;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class AmazonReportDocumentActionQueue extends Queue
{
    public function getQueueName(): string
    {
        return 'amazon-report-document-action';
    }

    public function getQueueDataClass(): string
    {
        return AmazonReportDocumentActionData::class;
    }

    /**
     * @param QueueDataInterface $queueData
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @return bool
     */
    public function handleQueueData(QueueDataInterface $queueData): bool
    {
        /**
         * @var AmazonReportDocumentActionData $queueData
         */
        $merchant_id = $queueData->getMerchantId();
        $merchant_store_id = $queueData->getMerchantStoreId();
        $report_type = $queueData->getReportType();
        $report_document_id = $queueData->getReportDocumentId();

        $logger = ApplicationContext::getContainer()->get(AmazonReportDocumentLog::class);

        $console = ApplicationContext::getContainer()->get(StdoutLoggerInterface::class);

        $logger->info(sprintf('Action Document 报告队列数据： %s', $queueData->toJson()));

        $file_base_name = $report_document_id;
        $file_path = sprintf('%s%s/%s/%s-%s/%s.txt', \Hyperf\Config\config('amazon.report_template_path'), 'scheduled', $report_type, $merchant_id, $merchant_store_id, $file_base_name);
        if (! file_exists($file_path)) {
            $log = sprintf('%s 文件不存在', $file_path);
            $console->error($log);
            $logger->error($log);
            return true;
        }

        try {
            $instance = ReportFactory::getInstance($merchant_id, $merchant_store_id, $report_type);

            $log = sprintf('Action %s 处理文件 %s', $report_type, $file_path);
            $console->info($log);
            $logger->info($log);

            $instance->run($report_document_id, $file_path);
        } catch (\Exception $exception) {
            $logger->error(sprintf('Action Document 报告队列数据：%s 出错。Error Message: %s', $queueData->toJson(), $exception->getMessage()));
            $console->error(sprintf('Action Document 报告队列数据：%s 出错。Error Message: %s', $queueData->toJson(), $exception->getMessage()));
        }

        return true;
    }

    public function safetyLine(): int
    {
        return 70;
    }
}
