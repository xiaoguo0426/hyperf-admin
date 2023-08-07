<?php

namespace App\Util\Log;

class AmazonCatalogLog extends AbstractLog
{
    public function __construct()
    {
        parent::__construct('log', 'amazon-catalog');
    }
}