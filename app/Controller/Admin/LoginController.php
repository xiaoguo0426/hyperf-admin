<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Exception\LoginException;
use App\Logic\Admin\LoginLogic;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use App\Validate\LoginValidate;
use App\Controller\Controller;

/**
 * @AutoController()
 * Class LoginController
 * @package App\Controller
 */
class LoginController extends Controller
{
    /**
     * @Inject()
     * @var LoginLogic
     */
    private $logic;

    public function index()
    {

        try {
            if (!$this->isPost()) {
                throw new \Exception('invalid access', 200);
            }

            $username = $this->request->post('username', '');//admin
            $password = $this->request->post('password', '');//123123

            $data = [
                'username' => $username,
                'password' => $password,
            ];

            $validate = new LoginValidate();

            if (!$validate->scene('login')->check($data)) {
                throw new \Exception($validate->getError());
            }

//            $logic = new LoginLogic();
            $tokens = $this->logic->login($username, $password);

            return $this->response->success($tokens, '登录成功！');

        } catch (LoginException $exception) {
            return $this->response->fail($exception->getCode(), $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->response->fail($exception->getCode(), $exception->getMessage());
        }

    }

    public function refreshToken()
    {

        try {

            $refresh_token = $this->request->header('refresh-token', '');

            $data = [
                'refresh_token' => $refresh_token,
            ];

            $validate = new LoginValidate();

            if (!$validate->scene('refreshToken')->check($data)) {
                throw new \Exception($validate->getError());
            }

            $tokens = $this->logic->refreshToken($refresh_token);

            return [
                'code' => 0,//成功
                'msg' => '刷新成功！',
                'data' => $tokens
            ];
        } catch (\Exception $throwable) {
//            var_dump($throwable->getFile());
//            var_dump($throwable->getLine());
//            var_dump($throwable->getMessage());
//            var_dump($throwable->getTraceAsString());
            return [
                'code' => -1,
                'msg' => $throwable->getMessage(),
                'data' => []
            ];
        }
    }

}