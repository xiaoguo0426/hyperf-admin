<?php


namespace App\Validate;


use think\Validate;

class UserValidate extends Validate
{

    protected $rule = [
        'id' => 'require',
        'role_id' => 'require',
        'username' => 'require|min:4|max:20',
        'nickname' => 'require|min:4|max:20',
        'gender' => 'require|in:0,1',
//        'avatar' => 'require',
        'mobile' => 'require|checkMobile',
        'email' => 'email',
        'remark' => '',
    ];

    protected $message = [
        'id.require' => '用户id不能为空！',
        'role_id.require' => '角色id不能为空！',
        'username.require' => '用户名不能为空！',
        'username.min' => '用户名长度不能少于4位有效字符！',
        'username.max' => '用户名长度不能大于20位有效字符！',
        'password.require' => '密码不能为空！',
        'password.min' => '密码长度不能少于4位有效字符！',
        'password.max' => '密码长度不能大于20位有效字符！',
        'nickname.min' => '昵称长度不能少于4位有效字符！',
        'nickname.max' => '昵称名称长度不能大于20位有效字符！',
        'gender.require' => '性别不能为空！',
        'gender.in' => '性别参数值有误！',
        'avatar' => '头像参数值不能为空！',
        'mobile.require' => '手机参数值不能为空！',
        'email' => '邮箱参数值有误！',
    ];

    protected $scene = [
        'base' => ['id'],
        'add' => ['username', 'password', 'role_id', 'nickname', 'gender', 'avatar', 'mobile', 'email'],
        'edit' => ['id', 'role_id', 'nickname', 'gender', 'avatar', 'mobile', 'email'],
    ];

    protected function checkMobile($fieldValue)
    {

        return true;
    }

}