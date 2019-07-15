<?php


namespace App\Service\Admin;

use App\Model\SystemUserModel;
use App\Service\BaseService;
use App\Util\AccessToken;
use App\Util\Prefix;

class LoginService extends BaseService
{
    /**
     * @param $username
     * @param $password
     * @return array
     * @throws \Exception
     */
    public function login($username, $password): array
    {

        $user = SystemUserModel::query()->where('username', $username)->first();

        if (empty($user)) {
            throw new \Exception('账号不存在！', 1);
        }
        if (0 === intval($user->status)) {
            throw new \Exception('账号已被禁用，请联系管理员！', 1);
        }

        $max_count = 5;//可重试次数

        $redis = $this->getRedis();

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
            'user_id' => $user->id,
            'user_name' => $user->username,
            'role' => $user->role
        ]);
        $token = $accessToken->createToken();

        $refresh_token = $accessToken->createRefreshToken();

        return compact('token', 'refresh_token');

    }

    public function refreshToken($refresh): string
    {

        $accessToken = new AccessToken();

        return $accessToken->refreshToken($refresh);

    }

}