<?php
declare(strict_types=1);

namespace App\Util;

class Auth
{

    /**
     * 忽略节点
     * @return array
     */
    public static function ignores(): array
    {
        return ['/', 'index/index', 'admin/login/index', 'admin/login/refreshToken'];
    }

    /**
     *
     * @param int $role_id
     * @return string
     */
    private static function authKey(int $role_id): string
    {
        return Prefix::authNodes($role_id);
    }

    /**
     * @return string
     */
    private static function ignoreNodesKey(): string
    {
        return Prefix::ignoreNodes();
    }

    public static function checkIgnoreNode(string $node): bool
    {
        $key = self::ignoreNodesKey();

        $redis = Redis::getInstance();
        var_dump($key);
        var_dump($node);
        var_dump(self::hash($node));
        var_dump($redis->sIsMember($key, self::hash($node)));
        return $redis->sIsMember($key, self::hash($node));

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
        //todo 基于redis的bitmap实现的权限校验  sad..没有实现这个功能，用集合方式实现

        $key = self::authKey($role_id);
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

        $key = self::authKey($role_id);

        $redis = Redis::getInstance();

        return $redis->sMembers($key);
    }

    /**
     * 系统所有节点
     * @return array|mixed
     */
    public static function getAllTreeNodes()
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

    /**
     * @param int $role_id
     * @param array $nodes
     * @return bool|int
     */
    public static function save(int $role_id, array $nodes)
    {
        $nodes = array_map(static function ($item) {
            return self::hash($item);
        }, $nodes);

        $key = self::authKey($role_id);

        return self::saveNodes($key, $nodes);
    }

    public static function saveIgnoreNodes(array $nodes)
    {
        var_dump($nodes);
        $nodes = array_map(static function ($item) {
            return self::hash($item);
        }, $nodes);
        var_dump($nodes);
        $key = self::ignoreNodesKey();

        return self::saveNodes($key, $nodes);
    }

    /**
     * @param string $key
     * @param array $nodes
     * @return bool|int
     */
    private static function saveNodes(string $key, array $nodes)
    {
        $redis = Redis::getInstance();

        $redis->del($key);//先移除，再新增

        return $redis->sAddArray($key, $nodes);
    }
}