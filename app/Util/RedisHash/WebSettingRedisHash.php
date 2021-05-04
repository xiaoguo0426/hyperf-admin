<?php

namespace App\Util\RedisHash;

use App\Util\Prefix;

/**
 * @property string site
 * @property string author
 * @property string domain
 * @property string keywords
 * @property string desc
 * @property string copyright
 * Class WebSettingRedisHash
 * @package App\Util\RedisHash
 */
class WebSettingRedisHash extends AbstractRedisHash
{
    public function __construct($connect = 'default')
    {
        $this->name = Prefix::webSetting();
        parent::__construct($connect);
    }
}