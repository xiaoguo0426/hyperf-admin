<?php


namespace App\Util;


class Auth
{

    /**
     * 忽略节点
     * @return array
     */
    public static function ignores()
    {
        return ['/admin/login/index', '/admin/login/refreshToken'];
    }

    /**
     * 检查节点权限
     * @param $node
     * @return bool
     */
    public static function checkNode($node)
    {
        //todo 如果当前登录会员是admin账号，则开放所有权限
        //todo 基于redis的bitmap实现的额权限校验
        return true;

    }

    /**
     * 是否已经登录
     */
    public static function isLogin()
    {

    }

}