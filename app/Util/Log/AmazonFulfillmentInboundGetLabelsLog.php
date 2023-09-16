<?php

namespace App\Util\Log;

class AmazonFulfillmentInboundGetLabelsLog extends AbstractLog
{
    public function __construct()
    {
        parent::__construct('get-labels', 'amazon-fulfillment-inbound');
    }
}