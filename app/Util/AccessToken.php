<?php


namespace App\Util;

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
    private static $sub;

    private static $rexp;

    const SCOPE_ROLE = 'role_access';

    const SCOPE_REFRESH = 'refresh_access';

    private static $app_key;

    private $data;

    public function __construct()
    {
        $this->_init();
    }

    private function _init()
    {
        $app_name = config('app_name', '');

        $app_key = config('app_key', '');

        if (empty($app_key) || empty($app_name)) {
            throw new LoginException('配置有误！', 1);
        }

        self::$app_key = $app_key;

        $cur_time = time();

        self::$iss = $app_name;//JWT 签发者
        self::$aud = 'api.onetech.site';//JWT 所面向的用户
        self::$sub = 'api.onetech.site';
        self::$iat = $cur_time;//JWT 的签发时间
        self::$nbf = $cur_time;//定义在什么时间之前，该 JWT 都是不可用的
        self::$jti = uuid(16);//JWT 的唯一身份标识，主要用来作为一次性 token, 从而回避重放攻击。
        self::$exp = $cur_time + 60;
        self::$alg = "HS256";

        self::$rexp = $cur_time + 86400;
    }

    public function encode(array $payload): string
    {
        return JWT::encode($payload, self::$app_key, self::$alg);
    }

    public function decode(string $jwt): array
    {
        try {
            $decode = JWT::decode($jwt, self::$app_key, [self::$alg]);

            return (array)$decode;
        } catch (ExpiredException $exception) {
            //过期token
            throw new LoginException('token过期！', -1);
        } catch (InvalidArgumentException $exception) {
            //参数错误
            throw new LoginException('token参数非法！', -1);
        } catch (\UnexpectedValueException $exception) {
            //token无效
            throw new LoginException('token无效！', -1);
        } catch (\Exception $exception) {
            throw new LoginException($exception->getMessage(), -1);
        }
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    private function _getPayload()
    {
        $payload = [
            "jti" => self::$jti,//JWT 的唯一身份标识，主要用来作为一次性 token, 从而回避重放攻击。
            "iss" => self::$iss,//JWT 签发者
            "sub" => self::$sub,//JWT 所面向的用户
            "aud" => self::$aud,//接收 JWT 的一方
            "iat" => self::$iat,//JWT 的签发时间
            "nbf" => self::$nbf,//定义在什么时间之前，该 JWT 都是不可用的
//            "exp" => self::$exp,//JWT 的过期时间，这个过期时间必须要大于签发时间
//            "scopes" => 'role_access',
            "data" => $this->data
        ];
        return $payload;
    }

    /**
     * 创建token
     * @return string
     */
    public function createToken()
    {

        $payload = $this->_getPayload();

        $payload['scopes'] = self::SCOPE_ROLE;
        $payload['exp'] = self::$rexp;

        return $this->encode($payload);
    }

    public function createRefreshToken()
    {
        $payload = $this->_getPayload();

        $payload['scopes'] = self::SCOPE_REFRESH;
        $payload['exp'] = self::$rexp;

        return $this->encode($payload);
    }

    public function checkToken(string $token): bool
    {
        if (empty($token)) {
            throw new LoginException('token不能为空！', -1);
        }

        $jwt = $this->decode($token);
        if (is_null($jwt)) {
            throw new LoginException('token无效！', -1);
        }

        if (self::SCOPE_ROLE !== $jwt['scopes']) {
            throw new LoginException('refresh-token参数非法！', -2);
        }

        return true;

    }

    /**
     * 刷新token
     * @param $refresh
     * @return string
     */
    public function refreshToken($refresh): string
    {
        if (empty($refresh)) {
            throw new LoginException('参数有误！');
        }

        $jwt = $this->decode($refresh);

        if (is_null($jwt)) {
            throw new LoginException('refresh-token参数有误！', -2);
        }

        if (self::SCOPE_REFRESH !== $jwt['scopes']) {
            throw new LoginException('refresh-token参数非法！', -2);
        }

        $data = $jwt['data'];

        $this->setData($data);

        return $this->createToken();
    }

}