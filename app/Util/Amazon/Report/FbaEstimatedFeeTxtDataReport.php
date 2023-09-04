<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Report;

use App\Model\AmazonReportFbaEstimatedFeeModel;
use App\Util\Log\AmazonReportLog;
use App\Util\RedisHash\AmazonFbaEstimatedFeeHash;

class FbaEstimatedFeeTxtDataReport extends ReportBase
{
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

            $data[$item['asin']][$item['currency']] = $item;
        }
        fclose($handle);

        $logger = di(AmazonReportLog::class);

        foreach ($data as $asin => $currency_items) {
            foreach ($currency_items as $currency_item) {
                $currency = $currency_item['currency'];
                $currency_item['product_name'] = preg_replace('/[^a-zA-Z0-9 ]/i', '', $currency_item['product_name']);
                $model = AmazonReportFbaEstimatedFeeModel::where('merchant_id', $merchant_id)
                    ->where('merchant_store_id', $merchant_store_id)
                    ->where('currency', $currency)
                    ->where('asin', $asin)->first();
                if (is_null($model)) {
                    $model = new AmazonReportFbaEstimatedFeeModel();
                    $model->merchant_id = $currency_item['merchant_id'];
                    $model->merchant_store_id = $currency_item['merchant_store_id'];
                    $model->country_id = $currency_item['country_id'];
                    $model->sku = $currency_item['sku'];
                    $model->fn_sku = $currency_item['fn_sku'];
                    $model->asin = $currency_item['asin'];
                    $model->product_name = $currency_item['product_name'];
                    $model->product_group = $currency_item['product_group'];
                    $model->brand = $currency_item['brand'];
                    $model->fulfilled_by = $currency_item['fulfilled_by'];
                    $model->your_price = $currency_item['your_price'];
                    $model->sales_price = $currency_item['sales_price'];
                    $model->longest_side = $currency_item['longest_side'];
                    $model->median_side = $currency_item['median_side'];
                    $model->shortest_side = $currency_item['shortest_side'];
                    $model->length_and_girth = $currency_item['length_and_girth'];
                    $model->unit_of_dimension = $currency_item['unit_of_dimension'];
                    $model->item_package_weight = $currency_item['item_package_weight'];
                    $model->unit_of_weight = $currency_item['unit_of_weight'];
                    $model->product_size_tier = $currency_item['product_size_tier'];
                    $model->currency = $currency_item['currency'];
                    $model->estimated_fee_total = $currency_item['estimated_fee_total'];
                    $model->estimated_referral_fee_per_unit = $currency_item['estimated_referral_fee_per_unit'];
                    $model->estimated_wariable_closing_fee = $currency_item['estimated_wariable_closing_fee'];
                    $model->estimated_order_handing_fee_per_order = $currency_item['estimated_order_handing_fee_per_order'];
                    $model->estimated_pick_pack_fee_per_unit = $currency_item['estimated_pick_pack_fee_per_unit'];
                    $model->save();

                    continue;
                }

                // 设置缓存
                $hash = \Hyperf\Support\make(AmazonFbaEstimatedFeeHash::class, [$merchant_id, $merchant_store_id, $currency_item['currency']]);
                $hash[$asin] = $currency_item['expected_fulfillment_fee_per_unit'];
            }
        }
        return true;
    }
}
