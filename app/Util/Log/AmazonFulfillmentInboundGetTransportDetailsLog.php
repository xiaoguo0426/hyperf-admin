<?php

namespace App\Util\Log;

class AmazonFulfillmentInboundGetTransportDetailsLog extends AbstractLog
{
    public function __construct()
    {
        parent::__construct('get-transport-details', 'amazon-fulfillment-inbound');
    }
}