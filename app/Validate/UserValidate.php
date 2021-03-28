<?php


namespace App\Validate;


use think\Validate;

class UserValidate extends Validate
{

    protected $rule = [
        'id' => 'require',
        'role_id' => 'require',
        'username' => 'require|min:4|max:20|alphaDash',
        'password' => 'require|min:4|max:20|alphaNum',//新密码或密码
        'oldPassword' => 'require|min:4|max:20|alphaNum',
        'rePassword' => 'require|min:4|max:20|alphaNum|confirm:password',//确认密码
        'nickname' => 'require|min:2|max:20',
        'gender' => 'require|in:0,1',
//        'avatar' => 'require',
        'mobile' => 'require|mobile',
        'email' => 'email',
        'remark' => '',
    ];

    protected $message = [
        'id.require' => '用户id不能为空！',
        'role_id.require' => '角色id不能为空！',
        'username.require' => '用户名不能为空！',
        'username.min' => '用户名长度不能少于4位有效字符！',
        'username.max' => '用户名长度不能大于20位有效字符！',
        'username.alphaDash' => '用户名只能是字母，数字，下划线或破折号',

        'password.require' => '密码不能为空！',
        'password.min' => '密码长度不能少于6位有效字符！',
        'password.max' => '密码长度不能大于20位有效字符！',
        'password.alphaNum' => '密码格式不正确！',

        'oldPassword.require' => '旧密码不能为空！',
        'oldPassword.min' => '旧密码长度不能少于6位有效字符！',
        'oldPassword.max' => '旧密码长度不能大于20位有效字符！',
        'oldPassword.alphaNum' => '旧密码格式不正确！',

        'rePassword.require' => '确认新密码不能为空！',
        'rePassword.min' => '确认新密码长度不能少于6位有效字符！',
        'rePassword.max' => '确认新密码长度不能大于20位有效字符！',
        'rePassword.alphaNum' => '确认新密码格式不正确！',
        'rePassword.confirm' => '新密码与确认新密码格式不一致！',

        'nickname.min' => '昵称长度不能少于2位有效字符！',
        'nickname.max' => '昵称名称长度不能大于20位有效字符！',
        'gender.require' => '性别不能为空！',
        'gender.in' => '性别参数值有误！',
        'avatar' => '头像参数值不能为空！',
        'mobile.require' => '手机参数值不能为空！',
        'mobile.mobile' => '手机参数值规则不正确！',
        'email' => '邮箱参数值有误！',
    ];

    protected $scene = [
        'base' => ['id'],
        'add' => ['username', 'password', 'role_id', 'nickname', 'gender', 'avatar', 'mobile', 'email'],
        'edit' => ['id', 'role_id', 'nickname', 'gender', 'avatar', 'mobile', 'email'],
        'password' => ['user_id', 'oldPassword', 'password', 'rePassword'],
    ];

    public function checkUsername($value, $rule, $data)
    {
        if (!preg_match('/^[a-zA-Z0-9]+$/', $value)) {
            return '用户名不能有特殊字符';
        }

        if (preg_match('/(^\_)|(\__)|(\_+$)/', $value)) {
            return '用户名首尾不能出现下划线\'_\'';
        }

        if (preg_match('/^\d+\d+\d$/', $value)) {
            return '用户名不能全为数字';
        }

        return true;
    }

}