<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Report;

use AmazonPHP\SellingPartner\Model\Reports\CreateReportSpecification;
use App\Util\Log\AmazonReportLog;
use Carbon\Carbon;
use Hyperf\Context\ApplicationContext;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

abstract class ReportBase implements ReportInterface
{
    public string $report_type;

    public int $merchant_id;

    public int $merchant_store_id;

    public ?Carbon $report_start_date;

    public ?Carbon $report_end_date;

    public array $header_map;

    protected string $dir;

    public function __construct(string $report_type, int $merchant_id, int $merchant_store_id)
    {
        $this->report_type = $report_type;

        $this->merchant_id = $merchant_id;
        $this->merchant_store_id = $merchant_store_id;

        $this->report_start_date = null;
        $this->report_end_date = null;

        $header_map = \Hyperf\Config\config('amazon_report_headers.' . $this->report_type);
        if (is_null($header_map)) {
            throw new \RuntimeException(sprintf('请在config/amazon_report_headers.php文件中配置该报告类型%s表头映射关系', $this->report_type));
        }

        $this->header_map = $header_map;
    }

    /**
     * 处理报告内容.
     */
    abstract public function run(string $report_id, string $file): bool;

    /**
     * 构造报告请求报告参数(如果某些报告有特定参数，需要重写该方法).
     */
    public function buildReportBody(string $report_type, array $marketplace_ids): CreateReportSpecification
    {
        return new CreateReportSpecification([
            'report_options' => null,
            'report_type' => $report_type, // 报告类型
            'data_start_time' => $this->getReportStartDate(), // 报告数据开始时间
            'data_end_time' => $this->getReportEndDate(), // 报告数据结束时间
            'marketplace_ids' => $marketplace_ids, // 市场标识符列表
        ]);
    }

    /**
     * 请求报告(如果特定报告有时间分组请求，需要重写该方法，参考SalesAndTrafficReportCustom.php报告).
     */
    public function requestReport(array $marketplace_ids, callable $func): void
    {
        is_callable($func) && $func($this, $this->report_type, $this->buildReportBody($this->report_type, $marketplace_ids), $marketplace_ids);
    }

    /**
     * 报告名称(如果特定报告有).
     */
    public function getReportFileName(array $marketplace_ids): string
    {
        return $this->report_type;
    }

    /**
     * 获得报告文件完整路径.
     */
    public function getReportFilePath(array $marketplace_ids): string
    {
        return $this->dir . $this->getReportFileName($marketplace_ids) . $this->getFileExt();
    }

    /**
     * 处理报告.
     */
    public function processReport(callable $func, array $marketplace_ids): void
    {
        if ($this->checkReportDate()) {
            throw new \InvalidArgumentException('Report Start/End Date Required,please check');
        }
        is_callable($func) && $func($this, $marketplace_ids);
    }

    /**
     * @param mixed $date
     * @throws \Exception
     */
    public function setReportStartDate($date): void
    {
        $this->report_start_date = $date ? new Carbon($date, 'UTC') : null;
    }

    public function getReportStartDate(): Carbon|null
    {
        return $this->report_start_date;
    }

    /**
     * @param mixed $date
     * @throws \Exception
     */
    public function setReportEndDate($date): void
    {
        $this->report_end_date = $date ? new Carbon($date, 'UTC') : null;
    }

    public function getReportEndDate(): Carbon|null
    {
        return $this->report_end_date;
    }

    /**
     * 报告是否需要指定开始时间与结束时间.
     */
    public function reportDateRequired(): bool
    {
        return false;
    }

    public function checkReportDate(): bool
    {
        if ($this->reportDateRequired()) {
            if (is_null($this->report_start_date) || is_null($this->report_end_date)) {
                return false;
            }
        }
        return true;
    }

    public function checkDir(): bool
    {
        $date = (new Carbon($this->getReportStartDate() ? $this->getReportStartDate()->format('Ymd') : '-1 day'))->setTimezone('UTC')->format('Ymd');
        // 检测report_type是哪个
        $category = $this->checkReportTypeCategory($this->report_type);

        $dir = sprintf('%s%s/%s/%s-%s/', \Hyperf\Config\config('amazon.report_template_path'), $category, $date, $this->merchant_id, $this->merchant_store_id);
        $this->dir = $dir;

        if (! is_dir($dir) && ! mkdir($dir, 0755, true)) {
            try {
                ApplicationContext::getContainer()->get(AmazonReportLog::class)->error(sprintf('Get Directory "%s" was not created', $dir));
            } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            }
        }

        return true;
    }

    public function getDir(): string
    {
        return $this->dir;
    }

    /**
     * 检查report_type属于哪个类型  requested|scheduled.
     */
    public function checkReportTypeCategory(string $report_type): string
    {
        $all = \Hyperf\Config\config('amazon_reports');
        foreach ($all as $type => $report_list) {
            foreach ($report_list as $report_type_raw) {
                if ($report_type_raw === $report_type) {
                    return $type;
                }
            }
        }
        throw new \InvalidArgumentException('Invalid Report Type,please check');
    }

    /**
     * 检查报告文件是否存在.
     */
    public function checkReportFile(array $marketplace_ids): bool
    {
        return file_exists($this->getReportFilePath($marketplace_ids));
    }

    public function getFileExt(): string
    {
        return '.txt';
    }
}
