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
use App\Model\AmazonReportSalesAndTrafficByAsinModel;
use App\Model\AmazonReportSalesAndTrafficByDateModel;
use App\Util\Log\AmazonReportActionLog;
use Carbon\Carbon;
use Hyperf\Context\ApplicationContext;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class SalesAndTrafficReport extends ReportBase
{
    /**
     * @throws \Exception
     */
    public function __construct(string $report_type, int $merchant_id, int $merchant_store_id)
    {
        parent::__construct($report_type, $merchant_id, $merchant_store_id);

        $start_time = Carbon::yesterday('UTC')->format('Y-m-d 00:00:00');
        $end_time = Carbon::yesterday('UTC')->format('Y-m-d 23:59:59');

        $this->setReportStartDate($start_time);
        $this->setReportEndDate($end_time);
    }

    /**
     * @throws \Exception
     */
    public function buildReportBody(string $report_type, array $marketplace_ids): CreateReportSpecification
    {
        return new CreateReportSpecification([
            'report_options' => [
                'dateGranularity' => 'DAY',
                'asinGranularity' => 'SKU',
            ],
            'report_type' => $report_type, // 报告类型
            'data_start_time' => $this->getReportStartDate(), // 报告数据开始时间
            'data_end_time' => $this->getReportEndDate(), // 报告数据结束时间
            'marketplace_ids' => $marketplace_ids, // 市场标识符列表
        ]);
    }

    /**
     * @throws \Exception
     */
    public function requestReport(array $marketplace_ids, callable $func): void
    {
        foreach ($marketplace_ids as $marketplace_id) {
            is_callable($func) && $func($this, $this->report_type, $this->buildReportBody($this->report_type, [$marketplace_id]), [$marketplace_id]);
        }
    }

    public function getReportFileName(array $marketplace_ids): string
    {
        return $this->report_type . '-' . $marketplace_ids[0];
    }

    /**
     * 处理报告.
     */
    public function processReport(callable $func, array $marketplace_ids): void
    {
        if (! $this->checkReportDate()) {
            throw new \InvalidArgumentException('Report Start/End Date Required,please check');
        }

        foreach ($marketplace_ids as $marketplace_id) {
            is_callable($func) && $func($this, [$marketplace_id]);
        }
    }

    /**
     * 请求该报告需要设置 开始时间和结束时间.
     */
    public function reportDateRequired(): bool
    {
        return true;
    }

    public function run(string $report_id, string $file): bool
    {
        $merchant_id = $this->merchant_id;
        $merchant_store_id = $this->merchant_store_id;

        $content = file_get_contents($file);

        try {
            $json = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $jsonException) {
            try {
                $logger = ApplicationContext::getContainer()->get(AmazonReportActionLog::class);
                $logger->error(sprintf('Action %s 解析错误 merchant_id: %s merchant_store_id: %s', $this->report_type, $merchant_id, $merchant_store_id));
            } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            }
            return true;
        }

        // 数据时间
        $data_time = $json['reportSpecification']['dataStartTime'];
        $marketplace_id = $json['reportSpecification']['marketplaceIds'][0];
        $salesAndTrafficByAsin = $json['salesAndTrafficByAsin'];

        foreach ($salesAndTrafficByAsin as $salesAndTraffic) {
            $parentAsin = $salesAndTraffic['parentAsin'];
            $childAsin = $salesAndTraffic['childAsin'];

            $salesByAsin = $salesAndTraffic['salesByAsin'];//销量

            $unitsOrdered = $salesByAsin['unitsOrdered'];
            $unitsOrderedB2B = $salesByAsin['unitsOrderedB2B'] ?? 0;

            $orderedProductSales = $salesByAsin['orderedProductSales'];
            $orderedProductSalesAmount = $orderedProductSales['amount'];
            $orderedProductSalesCurrencyCode = $orderedProductSales['currencyCode'];
            $orderedProductSalesB2B = $salesByAsin['orderedProductSalesB2B'] ?? null;
            $orderedProductSalesB2BAmount = 0;
            if (! is_null($orderedProductSalesB2B)) {
                $orderedProductSalesB2BAmount = $orderedProductSalesB2B['amount'];
                $orderedProductSalesB2BCurrencyCode = $orderedProductSalesB2B['currencyCode'];
            } else {
                $orderedProductSalesB2BCurrencyCode = $orderedProductSalesCurrencyCode;
            }
            $totalOrderItems = $salesByAsin['totalOrderItems'];
            $totalOrderItemsB2B = $salesByAsin['totalOrderItemsB2B'] ?? 0;

            $trafficByAsin = $salesAndTraffic['trafficByAsin'];//流量
            $browserSessions = $trafficByAsin['browserSessions'] ?? 0;
            $browserSessionsB2B = $trafficByAsin['browserSessionsB2B'] ?? 0;
            $mobileAppSessions = $trafficByAsin['mobileAppSessions'] ?? 0;
            $mobileAppSessionsB2B = $trafficByAsin['mobileAppSessionsB2B'] ?? 0;
            $sessions = $trafficByAsin['sessions'] ?? 0;
            $sessionsB2B = $trafficByAsin['sessionsB2B'] ?? 0;
            $browserSessionPercentage = $trafficByAsin['browserSessionPercentage'] ?? 0;
            $browserSessionPercentageB2B = $trafficByAsin['browserSessionPercentageB2B'] ?? 0;
            $mobileAppSessionPercentage = $trafficByAsin['mobileAppSessionPercentage'] ?? 0;
            $mobileAppSessionPercentageB2B = $trafficByAsin['mobileAppSessionPercentageB2B'] ?? 0;
            $sessionPercentage = $trafficByAsin['sessionPercentage'] ?? 0;
            $sessionPercentageB2B = $trafficByAsin['sessionPercentageB2B'] ?? 0;
            $browserPageViews = $trafficByAsin['browserPageViews'] ?? 0;
            $browserPageViewsB2B = $trafficByAsin['browserPageViewsB2B'] ?? 0;
            $mobileAppPageViews = $trafficByAsin['mobileAppPageViews'] ?? 0;
            $mobileAppPageViewsB2B = $trafficByAsin['mobileAppPageViewsB2B'] ?? 0;
            $pageViews = $trafficByAsin['pageViews'] ?? 0;
            $pageViewsB2B = $trafficByAsin['pageViewsB2B'] ?? 0;
            $browserPageViewsPercentage = $trafficByAsin['browserPageViewsPercentage'] ?? 0;
            $browserPageViewsPercentageB2B = $trafficByAsin['browserPageViewsPercentageB2B'] ?? 0;
            $mobileAppPageViewsPercentage = $trafficByAsin['mobileAppPageViewsPercentage'] ?? 0;
            $mobileAppPageViewsPercentageB2B = $trafficByAsin['mobileAppPageViewsPercentageB2B'] ?? 0;
            $pageViewsPercentage = $trafficByAsin['pageViewsPercentage'] ?? 0;
            $pageViewsPercentageB2B = $trafficByAsin['pageViewsPercentageB2B'] ?? 0;
            $buyBoxPercentage = $trafficByAsin['buyBoxPercentage'] ?? 0;
            $buyBoxPercentageB2B = $trafficByAsin['buyBoxPercentageB2B'] ?? 0;
            $unitSessionPercentage = $trafficByAsin['unitSessionPercentage'] ?? 0;
            $unitSessionPercentageB2B = $trafficByAsin['unitSessionPercentageB2B'] ?? 0;

            $model = AmazonReportSalesAndTrafficByAsinModel::query()
                ->where('merchant_id', $merchant_id)
                ->where('merchant_store_id', $merchant_store_id)
                ->where('marketplace_id', $marketplace_id)
                ->where('data_time', $data_time)
                ->where('parent_asin', $parentAsin)
                ->where('child_asin', $childAsin)
                ->first();

            if (is_null($model)) {
                $model = new AmazonReportSalesAndTrafficByAsinModel();
            }

            $model->merchant_id = $merchant_id;
            $model->merchant_store_id = $merchant_store_id;
            $model->marketplace_id = $marketplace_id;
            $model->data_time = $data_time;
            $model->parent_asin = $parentAsin;
            $model->child_asin = $childAsin;
            $model->units_ordered = $unitsOrdered;
            $model->units_ordered_b2b = $unitsOrderedB2B;
            $model->ordered_product_sales_amount = $orderedProductSalesAmount;
            $model->ordered_product_sales_currency_code = $orderedProductSalesCurrencyCode;
            $model->ordered_product_sales_b2b_amount = $orderedProductSalesB2BAmount;
            $model->ordered_product_sales_b2b_amount_currency_code = $orderedProductSalesB2BCurrencyCode;
            $model->total_order_items = $totalOrderItems;
            $model->total_order_items_b2b = $totalOrderItemsB2B;
            $model->browser_sessions = $browserSessions;
            $model->browser_sessions_b2b = $browserSessionsB2B;
            $model->mobile_app_sessions = $mobileAppSessions;
            $model->mobile_app_sessions_b2b = $mobileAppSessionsB2B;
            $model->sessions = $sessions;
            $model->sessions_b2b = $sessionsB2B;
            $model->browser_session_percentage = $browserSessionPercentage;
            $model->browser_session_percentage_b2b = $browserSessionPercentageB2B;
            $model->mobile_app_session_percentage = $mobileAppSessionPercentage;
            $model->mobile_app_session_percentage_b2b = $mobileAppSessionPercentageB2B;
            $model->session_percentage = $sessionPercentage;
            $model->session_percentage_b2b = $sessionPercentageB2B;
            $model->browser_page_views = $browserPageViews;
            $model->browser_page_views_b2b = $browserPageViewsB2B;
            $model->mobile_app_page_views = $mobileAppPageViews;
            $model->mobile_app_page_views_b2b = $mobileAppPageViewsB2B;
            $model->page_views = $pageViews;
            $model->page_views_b2b = $pageViewsB2B;
            $model->browser_page_views_percentage = $browserPageViewsPercentage;
            $model->browser_page_views_percentage_b2b = $browserPageViewsPercentageB2B;
            $model->mobile_app_page_views_percentage = $mobileAppPageViewsPercentage;
            $model->mobile_app_page_views_percentage_b2b = $mobileAppPageViewsPercentageB2B;
            $model->page_views_percentage = $pageViewsPercentage;
            $model->page_views_percentage_b2b = $pageViewsPercentageB2B;
            $model->buy_box_percentage = $buyBoxPercentage;
            $model->buy_box_percentage_b2b = $buyBoxPercentageB2B;
            $model->unit_session_percentage = $unitSessionPercentage;
            $model->unit_session_percentage_b2b = $unitSessionPercentageB2B;

            $model->save();
        }

        $salesAndTrafficByDate = $json['salesAndTrafficByDate'];
        foreach ($salesAndTrafficByDate as $salesAndTraffic) {
            $data_time = $salesAndTraffic['date'];
            $salesByDate = $salesAndTraffic['salesByDate'];

            $orderedProductSales = $salesByDate['orderedProductSales'];
            $orderedProductSalesAmount = $orderedProductSales['amount'];
            $orderedProductSalesCurrencyCode = $orderedProductSales['currencyCode'];

            $orderedProductSalesB2B = $salesByDate['orderedProductSalesB2B'] ?? null;
            $orderedProductSalesB2BAmount = 0;
            if (! is_null($orderedProductSalesB2B)) {
                $orderedProductSalesB2BAmount = $orderedProductSalesB2B['amount'];
                $orderedProductSalesB2BCurrencyCode = $orderedProductSalesB2B['currencyCode'];
            } else {
                $orderedProductSalesB2BCurrencyCode = $orderedProductSalesCurrencyCode;
            }

            $unitsOrdered = $salesByDate['unitsOrdered'];
            $unitsOrderedB2B = $salesByDate['unitsOrderedB2B'] ?? 0;
            $totalOrderItems = $salesByDate['totalOrderItems'];
            $totalOrderItemsB2B = $salesByDate['totalOrderItemsB2B'] ?? 0;

            $averageSalesPerOrderItem = $salesByDate['averageSalesPerOrderItem'];
            $averageSalesPerOrderItemAmount = $averageSalesPerOrderItem['amount'];
            $averageSalesPerOrderItemCurrencyCode = $averageSalesPerOrderItem['currencyCode'];

            $averageSalesPerOrderItemB2B = $salesByDate['averageSalesPerOrderItemB2B'] ?? null;
            $averageSalesPerOrderItemB2BAmount = 0;
            if (! is_null($averageSalesPerOrderItemB2B)) {
                $averageSalesPerOrderItemB2BAmount = $averageSalesPerOrderItemB2B['amount'];
                $averageSalesPerOrderItemB2BCurrencyCode = $averageSalesPerOrderItemB2B['currencyCode'];
            } else {
                $averageSalesPerOrderItemB2BCurrencyCode = $averageSalesPerOrderItemCurrencyCode;
            }

            $averageUnitsPerOrderItem = $salesByDate['averageUnitsPerOrderItem'] ?? 0;
            $averageUnitsPerOrderItemB2B = $salesByDate['averageUnitsPerOrderItemB2B'] ?? 0;

            $averageSellingPrice = $salesByDate['averageSellingPrice'];
            $averageSellingPriceAmount = $averageSellingPrice['amount'];
            $averageSellingPriceCurrencyCode = $averageSellingPrice['currencyCode'];

            $averageSellingPriceB2B = $salesByDate['averageSellingPriceB2B'] ?? null;
            $averageSellingPriceB2BAmount = 0;
            if (! is_null($averageSellingPriceB2B)) {
                $averageSellingPriceB2BAmount = $averageSellingPriceB2B['amount'];
                $averageSellingPriceB2BCurrencyCode = $averageSellingPriceB2B['currencyCode'];
            } else {
                $averageSellingPriceB2BCurrencyCode = $averageSellingPriceCurrencyCode;
            }

            $unitsRefunded = $salesByDate['unitsRefunded'];
            $refundRate = $salesByDate['refundRate'];
            $claimsGranted = $salesByDate['claimsGranted'];

            $claimsAmount = $salesByDate['claimsAmount'];
            $claimsAmountAmount = $claimsAmount['amount'];
            $claimsAmountCurrencyCode = $claimsAmount['currencyCode'];

            $shippedProductSales = $salesByDate['shippedProductSales'];
            $shippedProductSalesAmount = $shippedProductSales['amount'];
            $shippedProductSalesCurrencyCode = $shippedProductSales['currencyCode'];

            $unitsShipped = $salesByDate['unitsShipped'];
            $ordersShipped = $salesByDate['ordersShipped'];

            $trafficByDate = $salesAndTraffic['trafficByDate'];
            $browserPageViews = $trafficByDate['browserPageViews'] ?? 0;
            $browserPageViewsB2B = $trafficByDate['browserPageViewsB2B'] ?? 0;
            $mobileAppPageViews = $trafficByDate['mobileAppPageViews'] ?? 0;
            $mobileAppPageViewsB2B = $trafficByDate['mobileAppPageViewsB2B'] ?? 0;
            $pageViews = $trafficByDate['pageViews'] ?? 0;
            $pageViewsB2B = $trafficByDate['pageViewsB2B'] ?? 0;
            $browserSessions = $trafficByDate['browserSessions'] ?? 0;
            $browserSessionsB2B = $trafficByDate['browserSessionsB2B'] ?? 0;
            $mobileAppSessions = $trafficByDate['mobileAppSessions'] ?? 0;
            $mobileAppSessionsB2B = $trafficByDate['mobileAppSessionsB2B'] ?? 0;
            $sessions = $trafficByDate['sessions'] ?? 0;
            $sessionsB2B = $trafficByDate['sessionsB2B'] ?? 0;
            $buyBoxPercentage = $trafficByDate['buyBoxPercentage'] ?? 0;
            $buyBoxPercentageB2B = $trafficByDate['buyBoxPercentageB2B'] ?? 0;
            $orderItemSessionPercentage = $trafficByDate['orderItemSessionPercentage'] ?? 0;
            $orderItemSessionPercentageB2B = $trafficByDate['orderItemSessionPercentageB2B'] ?? 0;
            $unitSessionPercentage = $trafficByDate['unitSessionPercentage'] ?? 0;
            $unitSessionPercentageB2B = $trafficByDate['unitSessionPercentageB2B'] ?? 0;
            $averageOfferCount = $trafficByDate['averageOfferCount'] ?? 0;
            $averageParentItems = $trafficByDate['averageParentItems'] ?? 0;
            $feedbackReceived = $trafficByDate['feedbackReceived'] ?? 0;
            $negativeFeedbackReceived = $trafficByDate['negativeFeedbackReceived'] ?? 0;
            $receivedNegativeFeedbackRate = $trafficByDate['receivedNegativeFeedbackRate'] ?? 0;

            $model = AmazonReportSalesAndTrafficByDateModel::query()->where('merchant_id', $merchant_id)
                ->where('merchant_store_id', $merchant_store_id)
                ->where('marketplace_id', $marketplace_id)
                ->where('data_time', $data_time)
                ->first();
            if (is_null($model)) {
                $model = new AmazonReportSalesAndTrafficByDateModel();
            }

            $model->merchant_id = $merchant_id;
            $model->merchant_store_id = $merchant_store_id;
            $model->marketplace_id = $marketplace_id;
            $model->data_time = $data_time;
            $model->ordered_product_sales_amount = $orderedProductSalesAmount;
            $model->ordered_product_sales_currency_code = $orderedProductSalesCurrencyCode;
            $model->ordered_product_sales_b2b_amount = $orderedProductSalesB2BAmount;
            $model->ordered_product_sales_b2b_currency_code = $orderedProductSalesB2BCurrencyCode;
            $model->units_ordered = $unitsOrdered;
            $model->units_ordered_b2b = $unitsOrderedB2B;
            $model->total_order_items = $totalOrderItems;
            $model->total_order_items_b2b = $totalOrderItemsB2B;
            $model->average_sales_per_order_item_amount = $averageSalesPerOrderItemAmount;
            $model->average_sales_per_order_item_currency_code = $averageSalesPerOrderItemCurrencyCode;
            $model->average_sales_per_order_item_b2b_amount = $averageSalesPerOrderItemB2BAmount;
            $model->average_sales_per_order_item_b2b_currency_code = $averageSalesPerOrderItemB2BCurrencyCode;
            $model->average_units_per_order_item = $averageUnitsPerOrderItem;
            $model->average_units_per_order_item_b2b = $averageUnitsPerOrderItemB2B;
            $model->average_selling_price_amount = $averageSellingPriceAmount;
            $model->average_selling_price_currency_code = $averageSellingPriceCurrencyCode;
            $model->average_selling_price_b2b_amount = $averageSellingPriceB2BAmount;
            $model->average_selling_price_b2b_currency_code = $averageSellingPriceB2BCurrencyCode;
            $model->units_refunded = $unitsRefunded;
            $model->refund_rate = $refundRate;
            $model->claims_granted = $claimsGranted;
            $model->claims_amount_amount = $claimsAmountAmount;
            $model->claims_amount_currency_code = $claimsAmountCurrencyCode;
            $model->shipped_product_sales_amount = $shippedProductSalesAmount;
            $model->shipped_product_sales_currency_code = $shippedProductSalesCurrencyCode;
            $model->units_shipped = $unitsShipped;
            $model->orders_shipped = $ordersShipped;
            $model->browser_page_views = $browserPageViews;
            $model->browser_page_views_b2b = $browserPageViewsB2B;
            $model->mobile_app_page_views = $mobileAppPageViews;
            $model->mobile_app_page_views_b2b = $mobileAppPageViewsB2B;
            $model->page_views = $pageViews;
            $model->page_views_b2b = $pageViewsB2B;
            $model->browser_sessions = $browserSessions;
            $model->browser_sessions_b2b = $browserSessionsB2B;
            $model->mobile_app_sessions = $mobileAppSessions;
            $model->mobile_app_sessions_b2b = $mobileAppSessionsB2B;
            $model->sessions = $sessions;
            $model->sessions_b2b = $sessionsB2B;
            $model->buy_box_percentage = $buyBoxPercentage;
            $model->buy_box_percentage_b2b = $buyBoxPercentageB2B;
            $model->order_item_session_percentage = $orderItemSessionPercentage;
            $model->order_item_session_percentage_b2b = $orderItemSessionPercentageB2B;
            $model->unit_session_percentage = $unitSessionPercentage;
            $model->unit_session_percentage_b2b = $unitSessionPercentageB2B;
            $model->average_offer_count = $averageOfferCount;
            $model->average_parent_items = $averageParentItems;
            $model->feedback_received = $feedbackReceived;
            $model->negative_feedback_received = $negativeFeedbackReceived;
            $model->received_negative_feedback_rate = $receivedNegativeFeedbackRate;

            $model->save();
        }

        return true;
    }
}
