<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Exception;

class InvalidArgumentsException extends BaseException
{
    /**
     * InvalidArgumentsException constructor.
     */
    public function __construct(string $message = '参数错误', int $code = 1, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
