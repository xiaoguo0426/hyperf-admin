<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Util\AccessToken;
use App\Util\Prefix;
use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Annotation\AutoController;
use App\Validate\LoginValidate;
use App\Controller\BaseController;
use Hyperf\Redis\RedisFactory;

/**
 * @AutoController()
 * Class LoginController
 * @package App\Controller
 */
class LoginController extends BaseController
{

    public function index()
    {

        try {
            if (!$this->isPost()) {
                throw new \Exception('invalid access');
            }

            $username = $this->request->post('username', '');
            $password = $this->request->post('password', '');

            $data = [
                'username' => $username,
                'password' => $password,
            ];

            $validate = new LoginValidate();

            if (!$validate->scene('login')->check($data)) {
                throw new \Exception($validate->getError());
            }

            $user = Db::table('system_users')->where([
                'username' => $username,
            ])->select('*')->first();

            if (empty($user)) {
                throw new \Exception('账号不存在！');
            }
            if (0 == $user->status) {
                throw new \Exception('账号已被禁用，请联系管理员！');
            }

            $max_count = 5;//可重试次数

            $redis = $this->container->get(RedisFactory::class)->get('default');

            $key = Prefix::getLoginErrCount($username);

            $login_err_count = $redis->get($key);
            if (false === $login_err_count) {
                $login_err_count = 0;
                $redis->set($key, $login_err_count, 3600);
            }
            if ($login_err_count >= $max_count) {
                throw new \Exception('尝试次数达到上限，锁定一小时内禁止登录！');
            }
            //判断连续输错次数  可重试5次
            if (!password_verify($password, $user->password)) {
                //错误次数+1
                $redis->incr($key);
                $login_err_count++;
                $diff = $max_count - $login_err_count;

                if ($diff) {
                    throw new \Exception("账号或密码错误，还有{$diff}次尝试机会！");
                } else {
                    throw new \Exception('尝试次数达到上限，锁定一小时内禁止登录！');
                }
            }
            //清除错误次数
            $redis->del($key);
            //存入session

            $accessToken = new AccessToken();

            $jwt = $accessToken->createToken(12, $username, 'root');

            $this->setMsg('登录成功！');

            return $this->success(['token' => $jwt]);

        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }

    }

    public function refreshToken()
    {

        try {

            if (!$this->isPost()) {
                throw new \Exception('invalid access');
            }

            $token = $this->request->header('token', '');

            $data = [
                'token' => $token,
            ];

            $validate = new LoginValidate();

            if (!$validate->scene('refreshToken')->check($data)) {
                throw new \Exception($validate->getError());
            }

            $accessToken = new AccessToken();

            $refresh = $accessToken->refreshToken($token);

            return $this->success(['token' => $refresh]);

        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function test()
    {
        var_dump(123123);
    }

}