<?php

declare(strict_types=1);

namespace App\Util\OSS;

use Throwable;

class OssException extends \Exception
{
    public function __construct($message = 'param is empty', $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
