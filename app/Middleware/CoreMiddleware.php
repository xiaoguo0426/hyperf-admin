<?php
declare(strict_types=1);

namespace App\Middleware;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Utils\Contracts\Arrayable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CoreMiddleware extends \Hyperf\HttpServer\CoreMiddleware
{
    /**
     * Handle the response when cannot found any routes.
     *
     * @return array|Arrayable|mixed|ResponseInterface|string
     */
    protected function handleNotFound(ServerRequestInterface $request)
    {
        $std = di(StdoutLoggerInterface::class);
        $std->error('REQUEST NOT FOUND!');
        $std->error('Host: ' . $request->getHeaderLine('Host'));
        $std->error('X-Real-PORT: ' . $request->getHeaderLine('X-Real-PORT'));
        $std->error('X-Forwarded-For: ' . $request->getHeaderLine('X-Forwarded-For'));
        $std->error('x-real-ip: ' . $request->getHeaderLine('x-real-ip'));
        $std->error('referer: ' . $request->getHeaderLine('referer'));
        $std->error('Method: ' . $request->getMethod());
        $std->error('Path: ' . $request->getUri()->getPath());
        $std->error('Query: ' . $request->getUri()->getQuery());
        // 重写路由找不到的处理逻辑
        return $this->response()->withStatus(404);
    }

    /**
     * Handle the response when the routes found but doesn't match any available methods.
     *
     * @return array|Arrayable|mixed|ResponseInterface|string
     */
    protected function handleMethodNotAllowed(array $methods, ServerRequestInterface $request)
    {
        // 重写 HTTP 方法不允许的处理逻辑
        return $this->response()->withStatus(405);
    }
}