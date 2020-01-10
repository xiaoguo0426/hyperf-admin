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
        $std->warning('request not found!');
        $std->warning('Method: ' . $request->getMethod());
        $std->warning('x-real-ip: ' . var_export($request->getHeader('x-real-ip'), true));
        $std->warning('referer: ' . var_export($request->getHeader('referer'), true) );
        $std->warning('Path: ' . $request->getUri()->getPath());
        $std->warning('Query: ' . $request->getUri()->getQuery());
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