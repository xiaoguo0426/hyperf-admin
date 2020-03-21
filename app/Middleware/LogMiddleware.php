<?php
declare(strict_types=1);

namespace App\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\Logger\LoggerFactory;

/**
 * 日志中间件
 * Class LogMiddleware
 * @package App\Middleware
 */
class LogMiddleware implements MiddlewareInterface
{
    protected $logger;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        $logger = di(LoggerFactory::class)->make('request','request');//请求日志logger

        $uri = $request->getUri();

        $log = [
            'attributes' => $request->getAttributes(),
            'headers' => $request->getHeaders(),
            'method' => $request->getMethod(),
            'queryParams' => $request->getQueryParams(),
            'schema' => $uri->getScheme(),
            'host' => $uri->getHost(),
            'path' => $uri->getPath(),
            'body' => $request->getParsedBody()
        ];

        $logger->debug(var_export($log, true));

        return $handler->handle($request);
    }
}