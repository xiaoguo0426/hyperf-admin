<?php

declare(strict_types=1);

namespace App\Exception;

class InvalidRequestMethodException extends BaseException
{
    public function __construct($message = '请求方式错误！', $code = 1, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
