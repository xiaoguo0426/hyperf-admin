<?php
namespace App\Util\CacheTag;

use App\Util\Prefix;

class SendWithdrawEmailCacheTag extends CacheTag
{

    public function genKey($unique): string
    {
        return Prefix::sendWithdrawEmailCache($unique);
    }

}