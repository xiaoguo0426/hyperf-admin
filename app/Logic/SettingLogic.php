<?php

declare(strict_types=1);

namespace App\Logic;

use App\Util\RedisHash\SmtpSettingRedisHash;
use App\Util\RedisHash\WebSettingRedisHash;

class SettingLogic
{
    public function getWeb(): array
    {
        $hash = new WebSettingRedisHash();

        return $hash->toArray();
    }

    public function saveWeb($site, $author, $domain, $keywords, $desc, $copyright): bool
    {
        $hash = new WebSettingRedisHash();
        $hash->site = $site;
        $hash->author = $author;
        $hash->domain = $domain;
        $hash->keywords = $keywords;
        $hash->desc = $desc;
        $hash->copyright = $copyright;

        return true;
    }

    public function getSMTP(): array
    {
        $hash = new SmtpSettingRedisHash();
        return $hash->toArray();
    }

    public function saveSMTP($server, $port, $email, $nickname, $password): bool
    {
        $hash = new SmtpSettingRedisHash();
        $hash->server = $server;
        $hash->port = $port;
        $hash->email = $email;
        $hash->nickname = $nickname;
        $hash->password = $password;

        return true;
    }
}
