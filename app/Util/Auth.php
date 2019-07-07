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
        return ['/admin/login/index'];
    }

    /**
     * 检查节点权限
     * @param $node
     */
    public static function checkNode($node)
    {
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