<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Queue\Data;

class AmazonGetReportData extends QueueData
{
    private int $merchant_id;

    private int $merchant_store_id;

    private string $marketplace_ids;

    private string $report_id;

    private string $report_type;

    /**
     * 报告数据开始时间.
     * @var null|string
     */
    private string $data_start_time;

    /**
     * 报告数据结束时间.
     * @var null|string
     */
    private string $data_end_time;

    public function getMerchantId(): int
    {
        return $this->merchant_id;
    }

    public function setMerchantId(int $merchant_id): void
    {
        $this->merchant_id = $merchant_id;
    }

    public function getMerchantStoreId(): int
    {
        return $this->merchant_store_id;
    }

    public function setMerchantStoreId(int $merchant_store_id): void
    {
        $this->merchant_store_id = $merchant_store_id;
    }

    public function getMarketplaceIds(): array
    {
        return explode(',', $this->marketplace_ids);
    }

    public function setMarketplaceIds(array $marketplace_id): void
    {
        $this->marketplace_ids = implode(',', $marketplace_id);
    }

    public function getReportId(): string
    {
        return $this->report_id;
    }

    public function setReportId(string $report_id): void
    {
        $this->report_id = $report_id;
    }

    public function getReportType(): string
    {
        return $this->report_type;
    }

    public function setReportType(string $report_type): void
    {
        $this->report_type = $report_type;
    }

    /**
     * @return null|string
     */
    public function getDataStartTime(): string
    {
        return $this->data_start_time;
    }

    public function setDataStartTime(string $data_start_time): void
    {
        $this->data_start_time = $data_start_time;
    }

    /**
     * @return null|string
     */
    public function getDataEndTime(): string
    {
        return $this->data_end_time;
    }

    public function setDataEndTime(string $data_end_time): void
    {
        $this->data_end_time = $data_end_time;
    }

    public function toJson(): string
    {
        return json_encode([
            'merchant_id' => $this->merchant_id,
            'merchant_store_id' => $this->merchant_store_id,
            'marketplace_ids' => $this->marketplace_ids,
            'report_id' => $this->report_id,
            'report_type' => $this->report_type,
            'data_start_time' => $this->data_start_time,
            'data_end_time' => $this->data_end_time,
        ], JSON_THROW_ON_ERROR);
    }

    public function parse(array $arr): AmazonGetReportData
    {
        $this->setMerchantId($arr['merchant_id']);
        $this->setMerchantStoreId($arr['merchant_store_id']);
        $this->setMarketplaceIds(explode(',', $arr['marketplace_ids']));
        $this->setReportId($arr['report_id']);
        $this->setReportType($arr['report_type']);
        $this->setDataStartTime($arr['data_start_time']);
        $this->setDataEndTime($arr['data_end_time']);

        return $this;
    }
}
