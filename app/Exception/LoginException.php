<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Exception;

use Hyperf\Server\Exception\ServerException;

class LoginException extends ServerException
{
    public function __construct($message = '登录错误！', $code = 1, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
