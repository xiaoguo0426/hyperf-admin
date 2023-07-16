<?php

namespace App\Util\Log;

class AmazonReportActionLog extends AbstractLog
{
    public function __construct()
    {
        parent::__construct('Action', 'amazon-report');
    }
}