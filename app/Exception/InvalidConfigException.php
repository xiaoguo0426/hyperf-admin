<?php

declare(strict_types=1);

namespace App\Exception;

class InvalidConfigException extends BaseException
{
    public function __construct($message = '配置错误！', $code = 1, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
