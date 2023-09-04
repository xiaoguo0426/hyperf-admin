<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Exception\Handler;

use App\Exception\InvalidAccessException;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;

class InvalidAccessExceptionHandler extends ExceptionHandler
{
    /**
     * Handle the exception, and return the specified result.
     */
    public function handle(\Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        if ($throwable instanceof InvalidAccessException) {
            // 格式化输出
            $data = json_encode([
                'code' => $throwable->getCode(),
                'msg' => $throwable->getMessage(),
            ], JSON_UNESCAPED_UNICODE);

            // 阻止异常冒泡
            $this->stopPropagation();
            return $response->withStatus(200)->withBody(new SwooleStream($data));
        }

        return $response;
    }

    /**
     * Determine if the current exception handler should handle the exception,.
     *
     * @return bool
     *              If return true, then this exception handler will handle the exception,
     *              If return false, then delegate to next handler
     */
    public function isValid(\Throwable $throwable): bool
    {
        // TODO: Implement isValid() method.
        return true;
    }
}
