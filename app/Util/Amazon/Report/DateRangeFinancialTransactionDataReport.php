<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Report;

use App\Model\AmazonReportDateRangeFinancialTransactionDataModel;
use App\Util\ConsoleLog;
use App\Util\Log\AmazonReportDocumentLog;
use Carbon\Carbon;
use Hyperf\Collection\Collection;
use Hyperf\Context\ApplicationContext;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class DateRangeFinancialTransactionDataReport extends ReportBase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function run(string $report_id, string $file): bool
    {
        $currency_list = array_keys($this->header_map);

        $lineNumber = 2; // 指定的行号
        $splFileObject = new \SplFileObject($file, 'r');
        $splFileObject->seek($lineNumber - 1); // 转到指定行号的前一行
        $desiredLine = $splFileObject->current(); // 获取指定行的内容

        $config = [];
        $currency = '';
        foreach ($currency_list as $currency) {
            if (strpos($desiredLine, $currency) !== false) {
                $config = $this->header_map[$currency]; // 选择使用哪个货币对应的表头映射关系
                break;
            }
        }
        if (empty($config)) {
            // 请定义该货币对应的表头映射关系
            return true;
        }

        $locale = 'en';
        if ($currency === 'MXN') {
            // 时间解析语言
            $locale = 'es'; // 西班牙语
        }

        $merchant_id = $this->merchant_id;
        $merchant_store_id = $this->merchant_store_id;

        $logger = ApplicationContext::getContainer()->get(AmazonReportDocumentLog::class);
        $console = ApplicationContext::getContainer()->get(ConsoleLog::class);

        $handle = fopen($file, 'rb');
        // 前8行都是表头数据和报告描述信息，直接丢弃
        for ($i = 0; $i < 7; ++$i) {
            fgets($handle);
        }

        // 处理映射关系
        $explodes = explode(',', str_replace("\r\n", '', fgets($handle)));
        $headers = array_map(function ($val) {
            return trim(str_replace('"', '', $val));
        }, $explodes);

        $map = [];
        foreach ($headers as $index => $header) {
            if (! isset($config[$header])) {
                continue;
            }
            $map[$index] = $config[$header];
        }

        $cur_date = Carbon::now()->format('Y-m-d H:i:s');
        $collection = new Collection();

        while (! feof($handle)) {
            $fgets = fgets($handle);
            if ($fgets === false) {
                break;
            }

            $row = str_replace(["\r\n", ',,'], ['', ',"",'], trim($fgets));
            $explodes = explode('","', $row);
            $new = array_map(function ($val) {
                return trim(str_replace('"', '', $val));
            }, $explodes);
            $item = [];
            foreach ($map as $index => $value) {
                if (! isset($new[$index])) {
                    var_dump($index);
                    var_dump($value);
                    var_dump($new);
                    exit;
                }
                $val = $new[$index];
                if ($value === 'date') {
                    if ($locale === 'es') {
                        // 解析类似 12 abr 2023 16:26:06 GMT-7 格式时间
                        $val = Carbon::createFromLocaleFormat('d F Y H:i:s \G\M\TO', $locale, $val)->format('Y-m-d H:i:s');
                    } else {
                        // 解析类似 Jun 26, 2023 11:53:30 PM PDT 格式时间
                        $val = Carbon::createFromLocaleFormat('F j, Y H:i:s A T', $locale, $val)->format('Y-m-d H:i:s');
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
        fclose($handle);

        try {
            // 数据分片处理
            $collection->chunk(1000)->each(function (Collection $list) use ($merchant_id, $merchant_store_id) {
                try {
                    $final = []; // 写入的数据集合
                    foreach ($list as $item) {
                        $settlement_id = $item['settlement_id'];
                        $type = $item['type'];
                        $order_id = $item['order_id'];
                        $sku = $item['sku'];
                        $description = $item['description'];

                        $model = AmazonReportDateRangeFinancialTransactionDataModel::query()
                            ->where('merchant_id', $merchant_id)
                            ->where('merchant_store_id', $merchant_store_id)
                            ->where('settlement_id', $settlement_id);

                        if ($type === '' && $sku === '') {
                            // 优惠券类型付款报告数据，需要补充date条件。
                            $model->where('order_id', $order_id)
                                ->where('date', $item['date']);
                        } elseif ($type === 'Service Fee') {
                            $model->where('type', $type)
                                ->where('order_id', $order_id)
                                ->where('description', $item['description'])
                                ->where('date', $item['date']);
                        } elseif ($type === 'Fee Adjustment' && $sku === '') {
                            $model->where('type', $type)
                                ->where('order_id', $order_id)
                                ->where('total', $item['total']);
                        } elseif ($type === 'Order') {
                            $model->where('type', $type)
                                ->where('order_id', $order_id)
                                ->where('sku', $sku)
                                ->where('date', $item['date']);
                        } elseif ($type === 'FBA Inventory Fee') {
                            $model->where('type', $type)
                                ->where('order_id', $order_id)
                                ->where('description', $description)
                                ->where('date', $item['date'])
                                ->where('total', $item['total']);
                        } elseif ($type === 'FBA Customer Return Fee') {
                            $model->where('type', $type)
                                ->where('order_id', $order_id)
                                ->where('sku', $sku)
                                ->where('description', $description)
                                ->where('date', $item['date']);
                        } elseif ($type === 'Refund') {
                            $model->where('type', $type)
                                ->where('order_id', $order_id)
                                ->where('sku', $sku)
                                ->where('description', $description)
                                ->where('date', $item['date']);
                        }

                        $collection = $model->first();
                        if (is_null($collection)) {
                            $final[] = $item;
                        }
                    }

                    if (! empty($final)) {
                        AmazonReportDateRangeFinancialTransactionDataModel::insert($final);
                    }
                } catch (\Exception $exception) {
                }
            });
        } catch (\RuntimeException $runtimeException) {
            var_dump($runtimeException->getMessage());
            // 一旦出错，直接删除该文件，下一次重新拉取
            //            unlink($file);
        }

        return true;
    }
}
