<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
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
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->request->isMethod('post');
    }

    /**
     * 是否Get请求
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->request->isMethod('get');
    }

    /**
     * 是否为异步请求
     * @return bool
     */
    public function isAjax(): bool
    {
        return $this->request->getHeaderLine('x-requested-with') === 'XMLHttpRequest';
    }
}
