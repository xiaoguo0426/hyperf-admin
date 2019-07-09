<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Exception\LoginException;
use App\Util\AccessToken;
use App\Util\Prefix;
use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Annotation\AutoController;
use App\Validate\LoginValidate;
use Hyperf\Redis\RedisFactory;
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

            $user = Db::table('system_users')->where([
                'username' => $username,
            ])->select('*')->first();

            if (empty($user)) {
                throw new \Exception('账号不存在！', 1);
            }
            if (0 == $user->status) {
                throw new \Exception('账号已被禁用，请联系管理员！', 1);
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
                throw new \Exception('尝试次数达到上限，锁定一小时内禁止登录！', 1);
            }
            //判断连续输错次数  可重试5次
            if (!password_verify($password, $user->password)) {
                //错误次数+1
                $redis->incr($key);
                $login_err_count++;
                $diff = $max_count - $login_err_count;

                if ($diff) {
                    throw new \Exception("账号或密码错误，还有{$diff}次尝试机会！", 1);
                } else {
                    throw new \Exception('尝试次数达到上限，锁定一小时内禁止登录！', 1);
                }
            }
            //清除错误次数
            $redis->del($key);
            //存入session

            $accessToken = new AccessToken();

            $accessToken->setData([
                'user_id' => 12,
                'user_name' => $username,
                'role' => 'root'
            ]);
            $token = $accessToken->createToken();

            $refresh_token = $accessToken->createRefreshToken();

            $this->setMsg('登录成功！');

            return $this->success(compact('token', 'refresh_token'));

        } catch (LoginException $exception) {
            return $this->error($exception->getMessage(), $exception->getCode());
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), $exception->getCode());
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

            $accessToken = new AccessToken();

            $token = $accessToken->refreshToken($refresh_token);

            return $this->success(compact('token'));

        } catch (LoginException $exception) {
            return $this->error($exception->getMessage(), $exception->getCode());
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), $exception->getCode());
        }
    }

    public function test()
    {
        var_dump(123123);
    }

}