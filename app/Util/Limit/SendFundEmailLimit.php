<?php

declare(strict_types=1);

namespace App\Util\Limit;

use App\Util\Prefix;

class SendFundEmailLimit extends EmailLimit
{
    public function genKey($unique)
    {
        return Prefix::getSendFundEmailLimit($unique);
    }
}
