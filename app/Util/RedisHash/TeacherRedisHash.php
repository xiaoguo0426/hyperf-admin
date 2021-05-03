<?php


namespace App\Util\RedisHash;


class TeacherRedisHash extends AbstractRedisHash
{
    public function __construct($connect = 'default')
    {
        $this->name = 'teacher';
        parent::__construct($connect);
    }
}