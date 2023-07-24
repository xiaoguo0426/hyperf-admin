<?php

namespace App\Constants;

use Hyperf\Constants\Annotation\Constants;

#[Constants]
class AmazonConstants
{
    /**
     * @Message("Open")
     */
    public const FINANCE_GROUP_PROCESS_STATUS_OPEN = 'Open';
    /**
     * @Message("Closed")
     */
    public const FINANCE_GROUP_PROCESS_STATUS_CLOSED = 'Closed';
}