<?php

declare(strict_types=1);

namespace App\Util\RedisHash;

class StudentRedisHash extends AbstractRedisHash
{
    public function __construct($connect = 'default')
    {
        $this->name = 'student';
        parent::__construct($connect);
    }
}
