<?php

namespace App\Util\Amazon\Report;

use AmazonPHP\SellingPartner\Model\Reports\CreateReportSpecification;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Exception;

class FlatFileMfnSkuReturnAttributesReport extends ReportBase
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

}