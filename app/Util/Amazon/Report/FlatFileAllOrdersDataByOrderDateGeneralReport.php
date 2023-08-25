<?php

namespace App\Util\Amazon\Report;

use AmazonPHP\SellingPartner\Model\Reports\CreateReportSpecification;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Exception;

class FlatFileAllOrdersDataByOrderDateGeneralReport extends ReportBase
{

    public function run($file): bool
    {
        // TODO: Implement run() method.
        return true;
    }

}