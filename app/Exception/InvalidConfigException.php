<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Exception;

class InvalidConfigException extends BaseException
{
    public function __construct($message = '配置错误！', $code = 1, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
