<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Util\Token;
use App\Util\Auth;
use Hyperf\HttpServer\Contract\RequestInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;

/**
 * 权限验证中间件
 * Class AuthMiddleware
 * @package App\Middleware
 */
class AuthMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var HttpResponse
     */
    protected $response;

    public function __construct(ContainerInterface $container, HttpResponse $response, RequestInterface $request)
    {
        $this->container = $container;
        $this->response = $response;
        $this->request = $request;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        //检查节点
        //检查TOKEN
        $cur_node = substr($this->request->getUri()->getPath(), 1);
        foreach (Auth::ignores() as $ignore) {
            if ($ignore === $cur_node) {
                return $handler->handle($request);//交给下个一个中间件处理
            }
        }

        $token = $this->request->header('token', '');

        try {
            //todo 检查token
            $jwt = Token::instance()->checkToken($token);

            $admin = (array)($jwt->data);

            //todo 检查用户与节点权限
            if ('admin' !== $admin['user_name'] && !Auth::checkNode($admin['role_id'], $cur_node) && !Auth::checkIgnoreNode($cur_node)) {
                return $this->response->json(
                    [
                        'code' => '1',
                        'msg' => '您没有访问该节点的权限！',
                        'data' => []
                    ]
                );
            }
        } catch (\Throwable $throwable) {
            return $this->response->json(
                [
                    'code' => $throwable->getCode(),
                    'msg' => $throwable->getMessage(),
                    'data' => []
                ]
            );
        }

        return $handler->handle($request);//交给下个一个中间件处理
    }
}