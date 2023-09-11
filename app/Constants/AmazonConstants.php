<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

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

    /**
     * @Message("hour")
     */
    public const INTERVAL_TYPE_HOUR = 'hour';

    /**
     * @Message("day")
     */
    public const INTERVAL_TYPE_DAY = 'day';
}
