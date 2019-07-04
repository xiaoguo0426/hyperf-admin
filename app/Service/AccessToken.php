<?php


namespace App\Service;

use App\Exception\InvalidConfigException;
use App\Exception\LoginException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use InvalidArgumentException;

class AccessToken
{

    private static $iss;
    private static $aud;
    private static $iat;
    private static $nbf;
    private static $jti;
    private static $exp;
    private static $alg;

    private static $app_key;

    private $message;

    public function __construct()
    {
        $this->_init();
    }

    private function _init()
    {
        $app_name = config('app_name', '');

        $app_key = config('app_key', '');

        if (empty($app_key) || empty($app_name)) {
            throw new InvalidConfigException('配置有误！');
        }

        self::$app_key = $app_key;

        $cur_time = time();

        self::$iss = $app_name;//JWT 签发者
        self::$aud = 'api.onetech.site';//JWT 所面向的用户
        self::$iat = $cur_time;//JWT 的签发时间
        self::$nbf = $cur_time;//定义在什么时间之前，该 JWT 都是不可用的
        self::$jti = uuid(16);//JWT 的唯一身份标识，主要用来作为一次性 token, 从而回避重放攻击。
        self::$exp = $cur_time + 6 * 60 * 60;
        self::$alg = "HS256";
    }

    public function encode(array $payload): string
    {
        return JWT::encode($payload, self::$app_key, self::$alg);
    }

    public function decode(string $jwt)
    {
        try {
            $decode = json_decode(json_encode(JWT::decode($jwt, self::$app_key, [self::$alg])), true);

            return $decode;
        } catch (ExpiredException $exception) {
            //过期token
            $this->message = $exception->getMessage();
            return false;
        } catch (InvalidArgumentException $exception) {
            //参数错误
            $this->message = $exception->getMessage();
            return false;
        } catch (\Exception $exception) {
            //其他错误
            $this->message = $exception->getMessage();
            return false;
        }
    }

    public function getMessage()
    {
        return $this->message;
    }

    /**
     *
     * @param int $user_id
     */
    public function createToken(int $user_id, string $username, string $role)
    {

        if (empty($user_id) || empty($username) || empty($role)) {
            throw new LoginException('参数有误！');
        }

        $payload = [
            "jti" => self::$jti,//JWT 的唯一身份标识，主要用来作为一次性 token, 从而回避重放攻击。
            "iss" => self::$iss,//JWT 签发者
            "sub" => $user_id,//JWT 所面向的用户
            "aud" => self::$aud,//接收 JWT 的一方
            "iat" => self::$iat,//JWT 的签发时间
            "nbf" => self::$nbf,//定义在什么时间之前，该 JWT 都是不可用的
            "exp" => self::$exp//JWT 的过期时间，这个过期时间必须要大于签发时间
        ];

        $jwt = $this->encode($payload);
        echo $jwt;

        var_dump($this->decode($jwt));
    }

}