<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Queue\Data;

class AmazonActionReportData extends QueueData implements \JsonSerializable
{
    private int $merchant_id;

    private int $merchant_store_id;

    private string $marketplace_ids;

    /**
     * @var string 报告id
     */
    private string $report_id;

    /**
     * @var string 报告类型
     */
    private string $report_type;

    /**
     * 报告数据开始时间.
     */
    private ?string $data_start_time;

    /**
     * 报告数据结束时间.
     */
    private ?string $data_end_time;

    /**
     * @var string 报告保存路径
     */
    private string $report_file_path;

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

    public function getReportFilePath(): string
    {
        return $this->report_file_path;
    }

    public function setReportFilePath(string $report_file_path): void
    {
        $this->report_file_path = $report_file_path;
    }

    public function getDataStartTime(): string|null
    {
        return $this->data_start_time;
    }

    public function setDataStartTime(string|null $data_start_time): void
    {
        $this->data_start_time = $data_start_time;
    }

    public function getDataEndTime(): string|null
    {
        return $this->data_end_time;
    }

    public function setDataEndTime(string|null $data_end_time): void
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
            'report_file_path' => $this->report_file_path,
            'data_start_time' => $this->data_start_time,
            'data_end_time' => $this->data_end_time,
        ], JSON_THROW_ON_ERROR);
    }

    public function jsonSerialize(): array
    {
        return [
            'merchant_id' => $this->merchant_id,
            'merchant_store_id' => $this->merchant_store_id,
            'marketplace_ids' => $this->marketplace_ids,
            'report_id' => $this->report_id,
            'report_type' => $this->report_type,
            'report_file_path' => $this->report_file_path,
            'data_start_time' => $this->data_start_time,
            'data_end_time' => $this->data_end_time,
        ];
    }

    public function parse(array $arr): AmazonActionReportData
    {
        $this->setMerchantId($arr['merchant_id']);
        $this->setMerchantStoreId($arr['merchant_store_id']);
        $this->setMarketplaceIds(explode(',', $arr['marketplace_ids']));
        $this->setReportId($arr['report_id']);
        $this->setReportType($arr['report_type']);
        $this->setReportFilePath($arr['report_file_path']);
        $this->setDataStartTime($arr['data_start_time']);
        $this->setDataEndTime($arr['data_end_time']);

        return $this;
    }

    /**
     * @param mixed $json
     * @throws \JsonException
     */
    public static function fromJson($json): AmazonActionReportData
    {
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR); // 解码为关联数组
        return new self(
            $data['merchant_id'],
            $data['merchant_store_id'],
            $data['marketplace_ids'],
            $data['report_id'],
            $data['report_type'],
            $data['report_file_path'],
            $data['data_start_time'],
            $data['data_end_time']
        );
    }
}
