<?php

declare(strict_types=1);

namespace App\Exception;

class ResultException extends BaseException
{
    public function __construct($message = '操作失败！', $code = 1, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
