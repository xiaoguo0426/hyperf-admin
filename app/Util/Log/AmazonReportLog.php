<?php

namespace App\Util\Log;

class AmazonReportLog extends AbstractLog
{
    public function __construct()
    {
        parent::__construct('log', 'amazon-report');
    }
}