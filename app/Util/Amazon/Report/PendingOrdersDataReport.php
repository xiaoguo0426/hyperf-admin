<?php

namespace App\Util\Amazon\Report;

use AmazonPHP\SellingPartner\Model\Reports\CreateReportSpecification;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Exception;

class PendingOrdersDataReport extends ReportBase
{

    /**
     * @param string $report_id
     * @param string $file
     * @return bool
     */
    public function run(string $report_id, string $file): bool
    {
        // TODO: Implement run() method.
        return true;
    }

    /**
     * @throws Exception
     */
    public function buildReportBody(string $report_type, array $marketplace_ids): CreateReportSpecification
    {

        return new CreateReportSpecification([
            'report_options' => [
                'ShowSalesChannel' => 'true',
            ],
            'report_type' => $report_type,//报告类型
            'data_start_time' => $this->getReportStartDate(),//报告数据开始时间
            'data_end_time' => $this->getReportEndDate(),//报告数据结束时间
            'marketplace_ids' => $marketplace_ids//市场标识符列表
        ]);
    }
}