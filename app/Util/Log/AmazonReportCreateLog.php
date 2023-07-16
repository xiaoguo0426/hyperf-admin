<?php

namespace App\Util\Log;

class AmazonReportCreateLog extends AbstractLog
{
    public function __construct()
    {
        parent::__construct('Create', 'amazon-report');
    }
}