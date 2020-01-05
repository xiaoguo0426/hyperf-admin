<?php
declare(strict_types=1);

namespace App\Util;


use Hyperf\Redis\RedisFactory;
use Hyperf\Utils\ApplicationContext;

class Auth
{

    /**
     * 忽略节点
     * @return array
     */
    public static function ignores(): array
    {
        return ['/', 'index/index', '/admin/login/index', '/admin/login/refreshToken'];
    }

    /**
     * 检查节点权限
     * @param int $role_id
     * @param string $node
     * @return bool
     */
    public static function checkNode(int $role_id, string $node): bool
    {
        //todo 如果当前登录会员是admin账号，则开放所有权限
        //todo 基于redis的bitmap实现的额权限校验  sad..没有实现这个功能，用集合方式实现

        $key = Prefix::authNodes($role_id);

        $redis = Redis::getInstance();

        return $redis->sIsMember($key, self::hash($node));

    }

    /**
     * 获取角色的所有节点
     * @param int $role_id
     * @return array
     */
    public static function getNodes(int $role_id): array
    {

        $key = Prefix::authNodes($role_id);

        $redis = Redis::getInstance();

        return $redis->sMembers($key);
    }

    public static function getAllNodes()
    {
        $nodes_path = config('nodes_path');
        return file_exists($nodes_path) ? require $nodes_path : [];
    }

    /**
     * @param $node
     * @return string
     */
    public static function hash($node): string
    {
        return md5($node);
    }
}