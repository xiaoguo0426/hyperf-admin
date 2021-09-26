<?php

declare(strict_types=1);

namespace App\Util\RedisHash;

use App\Util\Prefix;

/**
 * @property string server
 * @property string port
 * @property string email
 * @property string nickname
 * @property string password
 * Class SmtpSettingRedisHash
 *
 * @package App\Util\RedisHash
 */
class SmtpSettingRedisHash extends AbstractRedisHash
{
    public function __construct($connect = 'default')
    {
        $this->name = Prefix::smtpSetting();
        parent::__construct($connect);
    }
}
