<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Report;

use App\Model\AmazonReportFulfilledShipmentsDataGeneralModel;
use App\Util\ConsoleLog;
use App\Util\Log\AmazonReportDocumentLog;
use Carbon\Carbon;
use Hyperf\Collection\Collection;
use Hyperf\Context\ApplicationContext;
use Hyperf\Database\Model\ModelNotFoundException;

class AmazonFulfilledShipmentsDataGeneralReport extends ReportBase
{
    public function run(string $report_id, string $file): bool
    {

        $logger = ApplicationContext::getContainer()->get(AmazonReportDocumentLog::class);
        $console = ApplicationContext::getContainer()->get(ConsoleLog::class);

        $merchant_id = $this->merchant_id;
        $merchant_store_id = $this->merchant_store_id;

        $config = $this->header_map;

        $splFileObject = new \SplFileObject($file, 'r');
        // 处理映射关系
        $headers = explode("\t", str_replace("\r\n", '', $splFileObject->fgets()));
        $map = [];
        foreach ($headers as $index => $header) {
            if (! isset($config[$header])) {
                continue;
            }
            $map[$index] = $config[$header];
        }

        $now = Carbon::now()->format('Y-m-d H:i:s');
        $data = [];
        while (! $splFileObject->eof()) {
            $row = explode("\t", str_replace("\r\n", '', $splFileObject->fgets()));
            $item = [];
            foreach ($map as $index => $value) {
                $val = trim($row[$index]);
                if ($value === 'estimated_arrival_date' || $value === 'payments_date' || $value === 'purchase_date' || $value === 'reporting_date' || $value === 'shipment_date') {
                    $val = str_replace('T', ' ', mb_substr($val, 0, 19));
                    if ($val === '') {
                        $val = null;
                    }
                } else if (in_array($value, ['item_price', 'item_tax', 'shipping_price', 'shipping_tax', 'gift_wrap_price', 'gift_wrap_tax'])) {
                    if ($val === '') {
                        $val = 0.00;
                    }
                }
                $item[$value] = $val;
            }
            $item['merchant_id'] = $merchant_id;
            $item['merchant_store_id'] = $merchant_store_id;
            $item['created_at'] = $now;

            $data[] = $item;
        }

        $collection = new Collection();
        foreach ($data as $item) {

            $amazon_order_id = $item['amazon_order_id'];
            $shipment_id = $item['shipment_id'];

            try {
                $model = AmazonReportFulfilledShipmentsDataGeneralModel::query()
                    ->where('merchant_id', $merchant_id)
                    ->where('merchant_store_id', $merchant_store_id)
                    ->where('amazon_order_id', $amazon_order_id)
                    ->where('shipment_id', $shipment_id)
                    ->firstOrFail();
            } catch (ModelNotFoundException $exception) {
                $collection->push($item);
                continue;
            }

            $model->merchant_order_id = $item['merchant_order_id'];
//            $model->shipment_id = $item['shipment_id'];
            $model->shipment_item_id = $item['shipment_item_id'];
            $model->amazon_order_item_id = $item['amazon_order_item_id'];
            $model->purchase_date = $item['purchase_date'];
            $model->payments_date = $item['payments_date'];
            $model->shipment_date = $item['shipment_date'];
            $model->reporting_date = $item['reporting_date'];
            $model->buyer_email = $item['buyer_email'];
            $model->buyer_name = $item['buyer_name'];
            $model->buyer_phone_number = $item['buyer_phone_number'];
            $model->product_name = $item['product_name'];
            $model->quantity_shipped = $item['quantity_shipped'];
            $model->currency = $item['currency'];
            $model->item_price = $item['item_price'];
            $model->item_tax = $item['item_tax'];
            $model->shipping_price = $item['shipping_price'];
            $model->shipping_tax = $item['shipping_tax'];
            $model->gift_wrap_price = $item['gift_wrap_price'];
            $model->gift_wrap_tax = $item['gift_wrap_tax'];
            $model->ship_service_level = $item['ship_service_level'];
            $model->recipient_name = $item['recipient_name'];
            $model->ship_address_1 = $item['ship_address_1'];
            $model->ship_address_2 = $item['ship_address_2'];
            $model->ship_address_3 = $item['ship_address_3'];
            $model->ship_city = $item['ship_city'];
            $model->ship_state = $item['ship_state'];
            $model->ship_postal_code = $item['ship_postal_code'];
            $model->ship_country = $item['ship_country'];
            $model->ship_phone_number = $item['ship_phone_number'];
            $model->bill_address_1 = $item['bill_address_1'];
            $model->bill_address_2 = $item['bill_address_2'];
            $model->bill_address_3 = $item['bill_address_3'];
            $model->bill_city = $item['bill_city'];
            $model->bill_state = $item['bill_state'];
            $model->bill_postal_code = $item['bill_postal_code'];
            $model->bill_country = $item['bill_country'];
            $model->item_promotion_discount = $item['item_promotion_discount'];
            $model->ship_promotion_discount = $item['ship_promotion_discount'];
            $model->carrier = $item['carrier'];
            $model->tracking_number = $item['tracking_number'];
            $model->estimated_arrival_date = $item['estimated_arrival_date'];
            $model->fulfillment_center_id = $item['fulfillment_center_id'];
            $model->fulfillment_channel = $item['fulfillment_channel'];
            $model->sales_channel = $item['sales_channel'];

            $model->save();
        }

        if ($collection->isNotEmpty()) {
            AmazonReportFulfilledShipmentsDataGeneralModel::insert($collection->all());
        }
        return true;
    }
}
