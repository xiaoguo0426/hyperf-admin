<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */
use App\Util\StdoutLogger;
use Hyperf\Contract\StdoutLoggerInterface;

return [
    Hyperf\HttpServer\CoreMiddleware::class => App\Middleware\CoreMiddleware::class,
    StdoutLoggerInterface::class => StdoutLogger::class,
];
