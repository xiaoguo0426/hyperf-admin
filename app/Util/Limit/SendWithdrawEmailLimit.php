<?php

declare(strict_types=1);

namespace App\Util\Limit;

use App\Util\Prefix;

class SendWithdrawEmailLimit extends EmailLimit
{
    public function genKey($unique): string
    {
        return Prefix::getSendWithdrawEmailLimit($unique);
    }
}
