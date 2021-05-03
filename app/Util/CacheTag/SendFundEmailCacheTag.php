<?php
namespace App\Util\CacheTag;

use App\Util\Prefix;

class SendFundEmailCacheTag extends CacheTag
{

    public function genKey($unique): string
    {
        return Prefix::sendFundEmailCache($unique);
    }

}