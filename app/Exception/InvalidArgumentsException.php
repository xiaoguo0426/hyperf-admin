<?php

declare(strict_types=1);

namespace App\Exception;

use Throwable;

class InvalidArgumentsException extends BaseException
{
    /**
     * InvalidArgumentsException constructor.
     */
    public function __construct(string $message = '参数错误', int $code = 1, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
