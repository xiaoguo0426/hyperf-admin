<?php

namespace App\Util\Log;

class AmazonReportGetLog extends AbstractLog
{
    public function __construct()
    {
        parent::__construct('Get', 'amazon-report');
    }
}