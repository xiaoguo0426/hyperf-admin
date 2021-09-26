<?php

declare(strict_types=1);

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 *
 * @document https://doc.hyperf.io
 *
 * @contact  group@hyperf.io
 *
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Kernel\Http;

use Hyperf\HttpMessage\Cookie\Cookie;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Utils\Context;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

class Response
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ResponseInterface
     */
    protected $response;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->response = $container->get(ResponseInterface::class);
    }

    /**
     * @param array $data
     */
    public function success(array $data, int $count = 0, string $msg = '获取成功！'): PsrResponseInterface
    {
        $res = [
            'code' => 0,
            'msg' => $msg,
            'data' => $data,
            'count' => $count,
        ];

        return $this->response->json($res);
    }

    public function fail($code, $message = ''): PsrResponseInterface
    {
        return $this->response->json([
            'code' => $code,
            'msg' => $message,
            'data' => [],
        ]);
    }

    public function redirect($url, $status = 302): \Hyperf\HttpMessage\Server\Response
    {
        return $this->response()
            ->withAddedHeader('Location', (string) $url)
            ->withStatus($status);
    }

    public function cookie(Cookie $cookie): Response
    {
        $response = $this->response()->withCookie($cookie);
        Context::set(PsrResponseInterface::class, $response);
        return $this;
    }

    public function response(): \Hyperf\HttpMessage\Server\Response
    {
        return Context::get(PsrResponseInterface::class);
    }
}
