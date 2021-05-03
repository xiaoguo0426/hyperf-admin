<?php


namespace App\Util\Limit;


use App\Util\Prefix;

class SendFundEmailLimit extends EmailLimit
{

    public function genKey($unique)
    {
        return Prefix::getSendFundEmailLimit($unique);
    }
}