<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Controller;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Container\ContainerInterface;

abstract class AbstractController
{
    #[Inject]
    protected ContainerInterface $container;

    #[Inject]
    protected RequestInterface $request;

    #[Inject]
    protected ResponseInterface $response;

    /**
     * 是否Post请求
     */
    public function isPost(): bool
    {
        return $this->request->isMethod('post');
    }

    /**
     * 是否Get请求
     */
    public function isGet(): bool
    {
        return $this->request->isMethod('get');
    }

    /**
     * 是否为异步请求
     */
    public function isAjax(): bool
    {
        return $this->request->getHeaderLine('x-requested-with') === 'XMLHttpRequest';
    }
}
