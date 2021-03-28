<?php


namespace App\Logic;


use App\Util\Prefix;
use App\Util\Redis;

class SettingLogic
{

    public function getWeb(): array
    {
        return Redis::getInstance()->hGetAll(Prefix::webSetting());
    }

    public function saveWeb($setting): bool
    {
        return Redis::getInstance()->hMSet(Prefix::webSetting(), $setting);
    }

    public function getSMTP(): array
    {
        return Redis::getInstance()->hGetAll(Prefix::smtpSetting());
    }

    public function saveSMTP($setting): bool
    {
        return Redis::getInstance()->hMSet(Prefix::smtpSetting(), $setting);
    }

}