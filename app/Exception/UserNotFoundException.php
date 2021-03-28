<?php
declare(strict_types=1);

namespace App\Exception;

class UserNotFoundException extends BaseException
{
    public function __construct($message = '用户不存在！', $code = 1, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}