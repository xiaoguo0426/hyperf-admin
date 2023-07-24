<?php

declare(strict_types=1);

namespace App\Exception;

class FileNotFoundException extends BaseException
{
    public function __construct($message = '文件不存在！', $code = 1, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
