<?php

namespace App\Util\Log;

class AmazonFinanceLog extends AbstractLog
{
    public function __construct()
    {
        parent::__construct('info', 'amazon-finance');
    }
}