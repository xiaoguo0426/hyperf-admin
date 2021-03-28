<?php


namespace App\Exception;


class InvalidAccessException extends BaseException
{
    public function __construct($message = '无权限访问！', $code = 1, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}