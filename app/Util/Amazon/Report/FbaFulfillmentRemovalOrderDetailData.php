<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Report;

class FbaFulfillmentRemovalOrderDetailData extends ReportBase
{
    /**
     * @throws \Exception
     */
    public function __construct(string $report_type, int $merchant_id, int $merchant_store_id)
    {
        parent::__construct($report_type, $merchant_id, $merchant_store_id);

        $start_time = date('Y-m-d 00:00:00', strtotime('-1 month'));
        $end_time = date('Y-m-d 00:00:00', strtotime('now'));
        $this->setReportStartDate($start_time);
        $this->setReportEndDate($end_time);
    }

    public function run(string $report_id, string $file): bool
    {
        $config = $this->header_map;

        $merchant_id = $this->merchant_id;
        $merchant_store_id = $this->merchant_store_id;

        $handle = fopen($file, 'rb');
        $header_line = str_replace("\r\n", '', fgets($handle));
        // 表头 需要处理换行符
        $headers = explode("\t", $header_line);

        $map = [];
        foreach ($headers as $index => $header) {
            if (! isset($config[$header])) {
                continue;
            }
            $map[$index] = $config[$header];
        }

        $data = [];
        while (! feof($handle)) {
            $row = explode("\t", str_replace("\r\n", '', fgets($handle)));
            $item = [];
            foreach ($map as $index => $value) {
                $item[$value] = $row[$index];
            }
            $item['merchant_id'] = $merchant_id;
            $item['merchant_store_id'] = $merchant_store_id;

            $data[] = $item;
        }
        fclose($handle);
        return true;
    }
}
