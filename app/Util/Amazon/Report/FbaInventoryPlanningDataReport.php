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
use App\Model\AmazonReportFbaInventoryPlanningDataModel;
use Exception;

class FbaInventoryPlanningDataReport extends ReportBase
{
    public function run(string $report_id, string $file): bool
    {
        $config = $this->header_map;

        $merchant_id = $this->merchant_id;
        $merchant_store_id = $this->merchant_store_id;

        $handle = fopen($file, 'rb');
        $header_line = str_replace("\r\n", '', fgets($handle)); // 表头 需要处理换行符
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
            $merchant_id = $item['merchant_id'];
            $merchant_store_id = $item['merchant_store_id'];
            $snapshot_date = $item['snapshot_date'];
            $sku = $item['sku'];
            $asin = $item['asin'];

            $collection = AmazonReportFbaInventoryPlanningDataModel::query()->where('merchant_id', $merchant_id)
                ->where('merchant_store_id', $merchant_store_id)
                ->where('snapshot_date', $snapshot_date)
                ->where('sku', $sku)
                ->where('asin', $asin)
                ->first();

            if (is_null($collection)) {
                $collection = new AmazonReportFbaInventoryPlanningDataModel();
            }

            $collection->merchant_id = $item['merchant_id'];
            $collection->merchant_store_id = $item['merchant_store_id'];
            $collection->snapshot_date = $item['snapshot_date'];
            $collection->sku = $item['sku'];
            $collection->fnsku = $item['fnsku'];
            $collection->asin = $item['asin'];
            $collection->product_name = $item['product_name'];
            $collection->condition = $item['condition'];
            $collection->available = $item['available'];
            $collection->pending_removal_quantity = $item['pending_removal_quantity'];
            $collection->inv_age_0_to_90_days = $item['inv_age_0_to_90_days'];
            $collection->inv_age_91_to_180_days = $item['inv_age_91_to_180_days'];
            $collection->inv_age_181_to_270_days = $item['inv_age_181_to_270_days'];
            $collection->inv_age_271_to_365_days = $item['inv_age_271_to_365_days'];
            $collection->inv_age_365_plus_days = $item['inv_age_365_plus_days'];
            $collection->currency = $item['currency'];
            $collection->units_shipped_t7 = $item['units_shipped_t7'];
            $collection->units_shipped_t30 = $item['units_shipped_t30'];
            $collection->units_shipped_t60 = $item['units_shipped_t60'];
            $collection->units_shipped_t90 = $item['units_shipped_t90'];
            $collection->alert = $item['alert'];
            $collection->your_price = $item['your_price'];
            $collection->sales_price = $item['sales_price'];
            $collection->lowest_price_new_plus_shipping = $item['lowest_price_new_plus_shipping'];
            $collection->lowest_price_used = $item['lowest_price_used'];
            $collection->recommended_action = $item['recommended_action'];
            $collection->healthy_inventory_level = $item['healthy_inventory_level'];
            $collection->recommended_sales_price = $item['recommended_sales_price'];
            $collection->recommended_sale_duration_days = $item['recommended_sale_duration_days'];
            $collection->recommended_removal_quantity = $item['recommended_removal_quantity'];
            $collection->estimated_cost_savings_of_recommended_actions = $item['estimated_cost_savings_of_recommended_actions'];
            $collection->sell_through = $item['sell_through'];
            $collection->item_volume = $item['item_volume'];
            $collection->volume_unit_measurement = $item['volume_unit_measurement'];
            $collection->storage_type = $item['storage_type'];
            $collection->storage_volume = $item['storage_volume'];
            $collection->marketplace = $item['marketplace'];
            $collection->product_group = $item['product_group'];
            $collection->sales_rank = $item['sales_rank'];
            $collection->days_of_supply = $item['days_of_supply'];
            $collection->estimated_excess_quantity = $item['estimated_excess_quantity'];
            $collection->weeks_of_cover_t30 = $item['weeks_of_cover_t30'];
            $collection->weeks_of_cover_t90 = $item['weeks_of_cover_t90'];
            $collection->featuredoffer_price = $item['featuredoffer_price'];
            $collection->sales_shipped_last_7_days = $item['sales_shipped_last_7_days'];
            $collection->sales_shipped_last_30_days = $item['sales_shipped_last_30_days'];
            $collection->sales_shipped_last_60_days = $item['sales_shipped_last_60_days'];
            $collection->sales_shipped_last_90_days = $item['sales_shipped_last_90_days'];
            $collection->inv_age_0_to_30_days = $item['inv_age_0_to_30_days'];
            $collection->inv_age_31_to_60_days = $item['inv_age_31_to_60_days'];
            $collection->inv_age_61_to_90_days = $item['inv_age_61_to_90_days'];
            $collection->inv_age_181_to_330_days = $item['inv_age_181_to_330_days'];
            $collection->inv_age_331_to_365_days = $item['inv_age_331_to_365_days'];
            $collection->estimated_storage_cost_next_month = $item['estimated_storage_cost_next_month'];
            $collection->inbound_quantity = $item['inbound_quantity'];
            $collection->inbound_working = $item['inbound_working'];
            $collection->inbound_shipped = $item['inbound_shipped'];
            $collection->inbound_received = $item['inbound_received'];
            $collection->no_sale_last_6_months = $item['no_sale_last_6_months'];
            $collection->reserved_quantity = $item['reserved_quantity'];
            $collection->unfulfillable_quantity = $item['unfulfillable_quantity'];
            $collection->quantity_to_be_charged_ais_181_210_days = $item['quantity_to_be_charged_ais_181_210_days'];
            $collection->estimated_ais_181_210_days = $item['estimated_ais_181_210_days'];
            $collection->quantity_to_be_charged_ais_211_240_days = $item['quantity_to_be_charged_ais_211_240_days'];
            $collection->estimated_ais_211_240_days = $item['estimated_ais_211_240_days'];
            $collection->quantity_to_be_charged_ais_241_270_days = $item['quantity_to_be_charged_ais_241_270_days'];
            $collection->estimated_ais_241_270_days = $item['estimated_ais_241_270_days'];
            $collection->quantity_to_be_charged_ais_271_300_days = $item['quantity_to_be_charged_ais_271_300_days'];
            $collection->estimated_ais_271_300_days = $item['estimated_ais_271_300_days'];
            $collection->quantity_to_be_charged_ais_301_330_days = $item['quantity_to_be_charged_ais_301_330_days'];
            $collection->estimated_ais_301_330_days = $item['estimated_ais_301_330_days'];
            $collection->quantity_to_be_charged_ais_331_365_days = $item['quantity_to_be_charged_ais_331_365_days'];
            $collection->estimated_ais_331_365_days = $item['estimated_ais_331_365_days'];
            $collection->quantity_to_be_charged_ais_365_plus_days = $item['quantity_to_be_charged_ais_365_plus_days'];
            $collection->estimated_ais_365_plus_days = $item['estimated_ais_365_plus_days'];

            $collection->save();
        }
        return true;
    }

    /**
     * @throws \Exception
     */
    public function buildReportBody(string $report_type, array $marketplace_ids): CreateReportSpecification
    {
        return new CreateReportSpecification([
            'report_type' => $report_type, // 报告类型
            'data_start_time' => $this->getReportStartDate(), // 报告数据开始时间
            'data_end_time' => $this->getReportEndDate(), // 报告数据结束时间
            'marketplace_ids' => $marketplace_ids, // 市场标识符列表
        ]);
    }

    //    /**
    //     * @param array $marketplace_ids
    //     * @param callable $func
    //     * @throws Exception
    //     */
    //    public function requestReport(array $marketplace_ids, callable $func): void
    //    {
    //        foreach ($marketplace_ids as $marketplace_id) {
    //            is_callable($func) && $func($this, $this->report_type, $this->buildReportBody($this->report_type, [$marketplace_id]), [$marketplace_id]);
    //        }
    //    }
    //
    //    public function getReportFileName(array $marketplace_ids): string
    //    {
    //        return $this->report_type . '-' . $marketplace_ids[0];
    //    }

    /**
     * 处理报告.
     */
    public function processReport(callable $func, array $marketplace_ids): void
    {
        if ($this->checkReportDate()) {
            throw new \InvalidArgumentException('Report Start/End Date Required,please check');
        }

        foreach ($marketplace_ids as $marketplace_id) {
            is_callable($func) && $func($this, [$marketplace_id]);
        }
    }
}
