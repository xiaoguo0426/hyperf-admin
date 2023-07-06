<?php

namespace App\Util\Amazon\Report;

use AmazonPHP\SellingPartner\Model\Reports\CreateReportScheduleSpecification;
use AmazonPHP\SellingPartner\Model\Reports\CreateReportSpecification;
use Exception;

abstract class ReportBase implements ReportInterface
{

    public string $report_type;

    public int $merchant_id;
    public int $merchant_store_id;

    public ?\DateTime $report_start_date;
    public ?\DateTime $report_end_date;

    public array $header_map;

    public function __construct(string $report_type, int $merchant_id, int $merchant_store_id)
    {
        $this->report_type = $report_type;

        $this->merchant_id = $merchant_id;
        $this->merchant_store_id = $merchant_store_id;

        $this->report_start_date = null;
        $this->report_end_date = null;

        $header_map = config('amazon_report_headers.' . $this->report_type);
        if (is_null($header_map)) {
            throw new \RuntimeException(sprintf('请在config/amazon_report_headers.php文件中配置该报告类型%s表头映射关系', $this->report_type));
        }

        $this->header_map = $header_map;

    }

    abstract public function run($file): void;

    /**
     * 构造报告请求报告参数(如果某些报告有特定参数，需要重写该方法)
     * @param string $report_type
     * @param array $marketplace_ids
     * @return CreateReportSpecification
     */
    public function buildReportBody(string $report_type, array $marketplace_ids): CreateReportSpecification
    {
        return new CreateReportSpecification([
            'report_options' => null,
            'report_type' => $report_type,//报告类型
            'data_start_time' => $this->getReportStartDate(),//报告数据开始时间
            'data_end_time' => $this->getReportEndDate(),//报告数据结束时间
            'marketplace_ids' => $marketplace_ids//市场标识符列表
        ]);
    }

    /**
     * 构造报告请求报告参数(如果某些报告有特定参数，需要重写该方法)
     * @param string $report_type
     * @param array $marketplace_ids
     * @return CreateReportScheduleSpecification
     */
    public function buildReportBodySchedule(string $report_type, array $marketplace_ids): CreateReportScheduleSpecification
    {
        return new CreateReportScheduleSpecification([
            'report_options' => null,
            'report_type' => $report_type,//报告类型
            'data_start_time' => $this->getReportStartDate(),//报告数据开始时间
            'data_end_time' => $this->getReportEndDate(),//报告数据结束时间
            'marketplace_ids' => $marketplace_ids//市场标识符列表
        ]);
    }

    /**
     * 请求报告(如果特定报告有时间分组请求，需要重写该方法，参考SalesAndTrafficReportCustom.php报告)
     * @param string $report_type
     * @param array $marketplace_ids
     * @param callable $func
     */
    public function requestReport(string $report_type, array $marketplace_ids, callable $func): void
    {
        is_callable($func) && $func($this->buildReportBody($report_type, $marketplace_ids), $marketplace_ids);
    }

    /**
     * 请求报告(如果特定报告有时间分组请求，需要重写该方法，参考SalesAndTrafficReportCustom.php报告)
     * @param string $report_type
     * @param array $marketplace_ids
     * @param callable $func
     */
    public function requestReportSchedule(string $report_type, array $marketplace_ids, callable $func): void
    {
        is_callable($func) && $func($this->buildReportBodySchedule($report_type, $marketplace_ids), $marketplace_ids);
    }

    /**
     * 报告名称(如果特定报告有)
     * @param array $marketplace_ids
     * @return string
     */
    public function getReportFileName(array $marketplace_ids): string
    {
        return $this->report_type;
    }

    /**
     * 处理报告
     * @param array $marketplace_ids
     * @param callable $func
     */
    public function processReport(callable $func, array $marketplace_ids): void
    {
        is_callable($func) && $func($marketplace_ids);
    }

    /**
     * @throws Exception
     */
    public function setReportStartDate($date): void
    {
        $this->report_start_date = new \DateTime($date, new \DateTimeZone('UTC'));
    }

    public function getReportStartDate(): ?\DateTime
    {
        return $this->report_start_date;
    }

    /**
     * @throws Exception
     */
    public function setReportEndDate($date): void
    {
        $this->report_end_date = new \DateTime($date, new \DateTimeZone('UTC'));
    }

    public function getReportEndDate(): ?\DateTime
    {
        return $this->report_end_date;
    }
}