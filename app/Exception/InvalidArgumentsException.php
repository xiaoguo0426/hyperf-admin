<?php
declare(strict_types=1);

namespace App\Exception;

use Throwable;

class InvalidArgumentsException extends \InvalidArgumentException
{
    /**
     * InvalidArgumentsException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = '参数错误', $code = 1, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}