<?php

declare(strict_types=1);

namespace App\Exception;

use Throwable;

class EmptyException extends BaseException
{
    public function __construct($message = '数据不存在！', $code = 200, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
