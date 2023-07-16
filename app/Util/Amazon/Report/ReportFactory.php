<?php

namespace App\Util\Amazon\Report;

class ReportFactory
{
    public static function getInstance(int $merchant_id, int $merchant_store_id, string $report_type): ReportBase
    {
        $class = match ($report_type) {
            'GET_RESTOCK_INVENTORY_RECOMMENDATIONS_REPORT' => RestockInventoryRecommendationsReport::class,
            'GET_AFN_INVENTORY_DATA' => AfnInventoryDataReport::class,
            'GET_FBA_MYI_UNSUPPRESSED_INVENTORY_DATA' => FbaMyiUnsuppressedInventoryDataReport::class,
            'GET_FBA_INVENTORY_PLANNING_DATA' => FbaInventoryPlanningDataReport::class,
            'GET_SELLER_FEEDBACK_DATA' => FeedbackDataReport::class,
            'GET_FBA_ESTIMATED_FBA_FEES_TXT_DATA' => FbaEstimatedFeeTxtDataReport::class,
            'GET_SALES_AND_TRAFFIC_REPORT' => SalesAndTrafficReport::class,
            'GET_SALES_AND_TRAFFIC_REPORT_CUSTOM' => SalesAndTrafficReportCustom::class,
            'GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA' => FbaFulfillmentCustomerReturnsData::class,
            'GET_FBA_REIMBURSEMENTS_DATA' => FbaReimbursementsData::class,
            'GET_FBA_FULFILLMENT_REMOVAL_ORDER_DETAIL_DATA' => FbaFulfillmentRemovalOrderDetailData::class,
            'GET_V2_SETTLEMENT_REPORT_DATA_FLAT_FILE' => V2SettlementReportDataFlatFile::class,
            'GET_V2_SETTLEMENT_REPORT_DATA_FLAT_FILE_V2' => V2SettlementReportDataFlatFileV2::class,
            'GET_V2_SELLER_PERFORMANCE_REPORT' => V2SellerPerformanceReport::class,
            'GET_DATE_RANGE_FINANCIAL_TRANSACTION_DATA' => DateRangeFinancialTransactionDataReport::class,
            default => throw new \RuntimeException(sprintf('请定义%s报告处理类', $report_type)),
        };

        return \Hyperf\Support\make($class, [$report_type, $merchant_id, $merchant_store_id]);
    }
}