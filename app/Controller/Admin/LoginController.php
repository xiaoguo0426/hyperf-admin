<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Exception\InvalidAccessException;
use App\Exception\InvalidArgumentsException;
use App\Exception\InvalidRequestMethodException;
use App\Logic\Admin\LoginLogic;
use Exception;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use App\Validate\LoginValidate;
use App\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface;

/**
 * @AutoController()
 * Class LoginController
 * @package App\Controller
 */
class LoginController extends AbstractController
{
    /**
     * @Inject()
     * @var LoginLogic
     */
    private $logic;

    /**
     * @return mixed
     * @ignore 登录
     */
    public function index()
    {

        if (!$this->isPost()) {
            throw new InvalidRequestMethodException();
        }

        $username = $this->request->post('username', '');//admin
        $password = $this->request->post('password', '');//123123

        $data = [
            'username' => $username,
            'password' => $password,
        ];

        $validate = di(LoginValidate::class);

        if (!$validate->scene('login')->check($data)) {
            throw new InvalidArgumentsException($validate->getError());
        }

        $tokens = $this->logic->login($username, $password);

        return $this->response->success($tokens, 0, '登录成功！');

    }

    /**
     * @return ResponseInterface
     * @throws Exception
     * @ignore 刷新token
     */
    public function refreshToken(): ResponseInterface
    {

        $refresh_token = $this->request->header('refresh-token', '');

        $data = [
            'refresh_token' => $refresh_token,
        ];

        $validate = di(LoginValidate::class);

        if (!$validate->scene('refreshToken')->check($data)) {
            throw new InvalidArgumentsException($validate->getError());
        }

        $tokens = $this->logic->refreshToken($refresh_token);

        return $this->response->success($tokens, 0, '刷新成功！');

    }

//    public function captcha()
//    {
//
//    }

}