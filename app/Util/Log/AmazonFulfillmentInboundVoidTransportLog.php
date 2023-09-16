<?php

namespace App\Util\Log;

class AmazonFulfillmentInboundVoidTransportLog extends AbstractLog
{
    public function __construct()
    {
        parent::__construct('void-transport', 'amazon-fulfillment-inbound');
    }
}