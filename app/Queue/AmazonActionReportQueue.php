<?php

namespace App\Queue;

use App\Queue\Data\AmazonActionReportData;
use App\Queue\Data\QueueDataInterface;
use App\Util\Amazon\Report\ReportFactory;
use App\Util\Log\AmazonReportLog;
use Exception;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class AmazonActionReportQueue extends Queue
{
    public function getQueueName(): string
    {
        return 'amazon-action-report';
    }

    public function getQueueDataClass(): string
    {
        return AmazonActionReportData::class;
    }

    /**
     * @param QueueDataInterface $queueData
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     * @return bool
     */
    public function handleQueueData(QueueDataInterface $queueData): bool
    {
        /**
         * @var AmazonActionReportData $queueData
         */
        $merchant_id = $queueData->getMerchantId();
        $merchant_store_id = $queueData->getMerchantStoreId();
        $marketplace_ids = $queueData->getMarketplaceIds();
        $report_type = $queueData->getReportType();
        $report_id = $queueData->getReportId();
        $report_file_path = $queueData->getReportFilePath();
        $data_start_time = $queueData->getDataStartTime();
        $data_end_time = $queueData->getDataEndTime();

        $console = ApplicationContext::getContainer()->get(StdoutLoggerInterface::class);
        $logger = ApplicationContext::getContainer()->get(AmazonReportLog::class);

        $logger->info(sprintf('Action 报告队列数据： %s', $queueData->toJson()));

        try {
            $instance = ReportFactory::getInstance($merchant_id, $merchant_store_id, $report_type);

            $instance->setReportStartDate($data_start_time);
            $instance->setReportEndDate($data_end_time);

            $log = sprintf('Action %s 处理文件 %s', $report_type, $report_file_path);
            $console->info($log);
            $logger->info($log);

            $instance->run($report_file_path);
        } catch (Exception $e) {
            $logger->error(sprintf('Action 报告队列数据：%s 出错。Error Message: %s', $queueData->toJson(), $e->getMessage()));
            $console->error(sprintf('Action 报告队列数据：%s 出错。Error Message: %s', $queueData->toJson(), $e->getMessage()));
        }

        return true;
    }

    /**
     * @return int
     */
    public function safetyLine(): int
    {
        return 70;
    }
}