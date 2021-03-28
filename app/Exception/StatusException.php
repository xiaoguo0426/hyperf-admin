<?php
declare(strict_types=1);

namespace App\Exception;


use Hyperf\Server\Exception\ServerException;

class StatusException extends ServerException
{
    public function __construct($message = '数据状态不正确！', $code = 1, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}