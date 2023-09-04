<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Middleware;

use Hyperf\Context\Context;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Router\Dispatched;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ServerRequestInterface;

class CoreMiddleware extends \Hyperf\HttpServer\CoreMiddleware
{
    public function dispatch(ServerRequestInterface $request): ServerRequestInterface
    {
        $path = $request->getUri()->getPath();
        // 实现 类似/product-category/get-list 风格路由
        if (str_contains($path, '-')) {
            $lstPoint = strrpos($path, '/');

            $method = substr($path, $lstPoint);

            $sub = explode('-', $method);
            array_walk($sub, static function (&$s): void {
                $s = ucfirst($s);
            });

            $method = lcfirst(implode($sub));

            $controller = substr($path, 0, $lstPoint);

            $path = str_replace('-', '_', $controller) . $method;
        }

        $routes = $this->dispatcher->dispatch($request->getMethod(), $path);

        $dispatched = new Dispatched($routes);

        return Context::set(ServerRequestInterface::class, $request->withAttribute(Dispatched::class, $dispatched));
    }

    protected function handleNotFound(ServerRequestInterface $request): mixed
    {
        $std = $this->stdLogger();
        $std->error('REQUEST NOT FOUND!');
        $std->error('Host: ' . $request->getHeaderLine('Host'));
        $std->error('Method: ' . $request->getMethod());
        $std->error('Path: ' . $request->getUri()->getPath());
        $std->error('Query: ' . $request->getUri()->getQuery());
        $std->error('X-Real-PORT: ' . $request->getHeaderLine('X-Real-PORT'));
        $std->error('X-Forwarded-For: ' . $request->getHeaderLine('X-Forwarded-For'));
        $std->error('x-real-ip: ' . $request->getHeaderLine('x-real-ip'));
        $std->error('referer: ' . $request->getHeaderLine('referer'));

        // 重写路由找不到的处理逻辑
        return $this->response()->withStatus(404);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function handleFound(Dispatched $dispatched, ServerRequestInterface $request): mixed
    {
        $t1 = microtime(true);

        if ($dispatched->handler->callback instanceof \Closure) {
            $response = \Hyperf\Support\call($dispatched->handler->callback);
        } else {
            [$controller, $action] = $this->prepareHandler($dispatched->handler->callback);
            $controllerInstance = $this->container->get($controller);
            if (! method_exists($controller, $action)) {
                // Route found, but the handler does not exist.
                return $this->response()->withStatus(500)->withBody(new SwooleStream('Method of class does not exist.'));
            }
            $parameters = $this->parseMethodParameters($controller, $action, $dispatched->params);
            $response = $controllerInstance->{$action}(...$parameters);
        }

        $t2 = microtime(true);

        $uri = $request->getUri();
        $this->stdLogger()->info(sprintf('[%s ms] [%s] %s', number_format(($t2 - $t1) * 1000, 3), $request->getMethod(), $uri->getPath() . ($uri->getQuery() ? '?' . $uri->getQuery() : '')));

        return $response;
    }

    protected function stdLogger()
    {
        return di(StdoutLoggerInterface::class);
    }
}
