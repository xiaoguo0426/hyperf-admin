<?php

namespace App\Util\Log;

class AmazonReportDocumentLog extends AbstractLog
{
    public function __construct()
    {
        parent::__construct('log', 'amazon-report-document');
    }
}