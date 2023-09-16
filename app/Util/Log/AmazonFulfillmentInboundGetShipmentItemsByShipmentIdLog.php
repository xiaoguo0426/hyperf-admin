<?php

namespace App\Util\Log;

class AmazonFulfillmentInboundGetShipmentItemsByShipmentIdLog extends AbstractLog
{
    public function __construct()
    {
        parent::__construct('get-shipment-items-by-shipment-id', 'amazon-fulfillment-inbound');
    }
}