<?php

namespace App\Util\Log;

class AmazonFulfillmentInboundGetPreorderInfoLog extends AbstractLog
{
    public function __construct()
    {
        parent::__construct('get-preorder-info', 'amazon-fulfillment-inbound');
    }
}