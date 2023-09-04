<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Report;

use App\Model\AmazonReportFbaReimbursementsDataModel;
use Carbon\Carbon;

class FbaReimbursementsData extends ReportBase
{
    public array $date_list = [];

    public function __construct(string $report_type, int $merchant_id, int $merchant_store_id)
    {
        parent::__construct($report_type, $merchant_id, $merchant_store_id);

        $last_7days_start_time = Carbon::now('UTC')->subDays(15)->format('Y-m-d 00:00:00'); // 最近7天
        $last_end_time = Carbon::yesterday('UTC')->format('Y-m-d 23:59:59');

        $this->date_list = [
            [
                'start_time' => $last_7days_start_time,
                'end_time' => $last_end_time,
            ],
        ];
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

        foreach ($data as $item) {
            $collection = AmazonReportFbaReimbursementsDataModel::query()
                ->where('merchant_id', $merchant_id)
                ->where('merchant_store_id', $merchant_store_id)
                ->where('reimbursement_id', $item['reimbursement_id'])
                ->first();

            if (is_null($collection)) {
                $collection = new AmazonReportFbaReimbursementsDataModel();
            }

            $collection->merchant_id = $merchant_id;
            $collection->merchant_store_id = $merchant_store_id;
            $collection->approval_date = isset($item['approval_date']) ? Carbon::parse($item['approval_date'])->format('Y-m-d H:i:s') : null;
            $collection->reimbursement_id = $item['reimbursement_id'];
            $collection->case_id = $item['case_id'] ?? '';
            $collection->amazon_order_id = $item['amazon_order_id'] ?? '';
            $collection->reason = $item['reason'] ?? '';
            $collection->sku = $item['sku'];
            $collection->fnsku = $item['fnsku'] ?? '';
            $collection->asin = $item['asin'] ?? '';
            $collection->product_name = $item['product_name'] ?? '';
            $collection->condition = $item['condition'] ?? '';
            $collection->currency_unit = $item['currency_unit'] ?? '';
            $collection->amount_per_unit = $item['amount_per_unit'] ?? 0;
            $collection->amount_total = $item['amount_total'] ?? 0;
            $collection->quantity_reimbursed_cash = $item['quantity_reimbursed_cash'] ?? 0;
            $collection->quantity_reimbursed_inventory = $item['quantity_reimbursed_inventory'] ?? 0;
            $collection->quantity_reimbursed_total = $item['quantity_reimbursed_total'] ?? 0;
            $collection->original_reimbursement_id = $item['original_reimbursement_id'] ?? 0;
            $collection->original_reimbursement_type = $item['original_reimbursement_type'] ?? 0;

            $collection->save();
        }

        return true;
    }

    /**
     * 请求报告.
     * @throws \Exception
     */
    public function requestReport(array $marketplace_ids, callable $func): void
    {
        foreach ($this->date_list as $key => $item) {
            $this->setReportStartDate($item['start_time']);
            $this->setReportEndDate($item['end_time']);

            foreach ($marketplace_ids as $marketplace_id) {
                is_callable($func) && $func($this, $this->report_type, $this->buildReportBody($this->report_type, [$marketplace_id]), [$marketplace_id]);
            }
        }
    }

    /**
     * 处理报告.
     * @throws \Exception
     * @deprecated
     */
    public function processReport(callable $func, array $marketplace_ids): void
    {
        foreach ($this->date_list as $key => $item) {
            $this->setReportStartDate($item['start_time']);
            $this->setReportEndDate($item['end_time']);

            foreach ($marketplace_ids as $marketplace_id) {
                is_callable($func) && $func($this, [$marketplace_id]);
            }
        }
    }
}
