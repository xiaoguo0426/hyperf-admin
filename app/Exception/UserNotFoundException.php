<?php


namespace App\Exception;

use Hyperf\Server\Exception\ServerException;

class UserNotFoundException extends ServerException
{
    public function __construct($message = "用户不存在！", $code = 200, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}