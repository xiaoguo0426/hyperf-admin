<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Exception\LoginException;
use App\Service\Admin\LoginService;
use App\Util\AccessToken;
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

    public function index()
    {

        try {
            if (!$this->isPost()) {
                throw new \Exception('invalid access', 1);
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

            $service = new LoginService();

            $tokens = $service->login($username, $password);

            return [
                'code' => 0,//成功
                'msg' => '登录成功！',
                'data' => $tokens
            ];
        } catch (LoginException $exception) {
            return [
                'code' => $exception->getCode(),
                'msg' => $exception->getMessage(),
                'data' => []
            ];
        } catch (\Exception $exception) {
            return [
                'code' => $exception->getCode(),
                'msg' => $exception->getMessage(),
                'data' => []
            ];
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

            $service = new LoginService();

            $token = $service->refreshToken($refresh_token);

            return [
                'code' => 0,//成功
                'msg' => '刷新成功！',
                'data' => compact('token')
            ];
        } catch (LoginException $exception) {
            return [
                'code' => $exception->getCode(),
                'msg' => $exception->getMessage(),
                'data' => []
            ];
        } catch (\Exception $exception) {
            return [
                'code' => $exception->getCode(),
                'msg' => $exception->getMessage(),
                'data' => []
            ];
        }
    }

}