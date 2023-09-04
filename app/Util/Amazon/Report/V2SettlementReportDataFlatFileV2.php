<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Report;

use App\Model\AmazonSettlementReportDataFlatFileV2Model;
use App\Util\ConsoleLog;
use App\Util\Log\AmazonReportDocumentLog;
use Carbon\Carbon;
use Hyperf\Collection\Collection;
use Hyperf\Context\ApplicationContext;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class V2SettlementReportDataFlatFileV2 extends ReportBase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function run(string $report_id, string $file): bool
    {
        $currency_list = [
            'USD',
            'CAD',
            'MXN',
        ];
        $config = $this->header_map;

        $merchant_id = $this->merchant_id;
        $merchant_store_id = $this->merchant_store_id;

        $logger = ApplicationContext::getContainer()->get(AmazonReportDocumentLog::class);
        $console = ApplicationContext::getContainer()->get(ConsoleLog::class);

        $splFileObject = new \SplFileObject($file, 'r');
        $header_line = str_replace("\r\n", '', $splFileObject->fgets());
        $headers = explode("\t", trim($header_line));

        $map = [];
        foreach ($headers as $index => $header) {
            if (! isset($config[$header])) {
                continue;
            }
            $map[$index] = $config[$header];
        }

        $summary_line = str_replace("\r\n", '', $splFileObject->fgets()); // 统计行数据不入库
        foreach ($currency_list as $currency) {
            if (strpos($summary_line, $currency) !== false) {
                break;
            }
        }

        $cur_date = Carbon::now()->format('Y-m-d H:i:s');
        $collection = new Collection();

        $splFileObject->seek(1); // 从第2行开始读取数据
        while (! $splFileObject->eof()) {
            $fgets = str_replace("\r\n", '', $splFileObject->fgets());
            if ($fgets === '') {
                continue;
            }
            $row = explode("\t", $fgets);
            foreach ($map as $index => $value) {
                $val = trim($row[$index] ?? '');
                if ($val) {
                    // 这几个字段如果不为空，则需要根据不同地区的日期格式作处理
                    if ($value === 'settlement_start_date' || $value === 'settlement_end_date' || $value === 'deposit_date' || $value === 'posted_date_time') {
                        if ($currency === 'USD') {
                            $val = Carbon::createFromFormat('Y-m-d H:i:s T', $val)->format('Y-m-d H:i:s');
                        } elseif ($currency === 'CAD') {
                            $val = Carbon::createFromFormat('d.m.Y H:i:s T', $val)->format('Y-m-d H:i:s');
                        } elseif ($currency === 'MXN') {
                            $val = Carbon::createFromFormat('d.m.Y H:i:s T', $val)->format('Y-m-d H:i:s');
                        }
                    }
                } else {
                    // 这几个字段如果为空，该字段值需要赋值为null
                    if ($value === 'settlement_start_date' || $value === 'settlement_end_date' || $value === 'deposit_date' || $value === 'posted_date_time') {
                        $val = null;
                    }
                }

                $item[$value] = $val;
            }
            $item['merchant_id'] = $merchant_id;
            $item['merchant_store_id'] = $merchant_store_id;
            $item['created_at'] = $cur_date;
            $item['updated_at'] = $cur_date;
            $collection->push($item);
        }

        $collection->chunk(1000)->each(function (Collection $list) {
            $final = []; // 写入的数据集合

            foreach ($list as $item) {
                $merchant_id = $item['merchant_id'];
                $merchant_store_id = $item['merchant_store_id'];
                $settlement_id = $item['settlement_id'];
                $order_id = $item['order_id'];
                $transaction_type = $item['transaction_type'];
                $amount_type = $item['amount_type'];
                $amount_description = $item['amount_description'];
                $sku = $item['sku'];

                $model = AmazonSettlementReportDataFlatFileV2Model::where('merchant_id', $merchant_id)
                    ->where('merchant_store_id', $merchant_store_id)
                    ->where('settlement_id', $settlement_id)
                    ->where('order_id', $order_id)
                    ->where('transaction_type', $transaction_type)
                    ->where('amount_type', $amount_type)
                    ->where('amount_description', $amount_description);

                if ($transaction_type === 'Order' && ($amount_type === 'ItemFees' || $amount_type === 'ItemPrice' || $amount_type === 'ItemWithheldTax' || $amount_type === 'Promotion')) {
                    $model->where('sku', $sku);
                } elseif ($transaction_type === 'CouponRedemptionFee' && $amount_type === 'CouponRedemptionFee') {
                    $posted_date_time = $item['posted_date_time'];
                    $model->where('posted_date_time', $posted_date_time);
                }

                $collection = $model->first();
                if (is_null($collection)) {
                    $final[] = $item;
                }
            }

            if (! empty($final)) {
                $insert = AmazonSettlementReportDataFlatFileV2Model::insert($final);
            }
        });

        return true;
    }
}
