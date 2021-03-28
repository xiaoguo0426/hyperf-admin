<?php
declare(strict_types=1);

namespace App\Validate;

use think\Validate;

class LoginValidate extends Validate
{

    protected $rule = [
        'username' => 'require|min:4',
        'password' => 'require|min:4',
        'token' => 'require',
        'refresh_token' => 'require'
    ];

    protected $message = [
        'username.require' => '登录账号不能为空！',
        'username.min' => '登录账号长度不能少于4位有效字符！',
        'password.require' => '登录密码不能为空！',
        'password.min' => '登录密码长度不能少于4位有效字符！',
        'token.require' => 'token参数缺失！',
        'refresh_token.require'=>'refresh_token参数缺失！'
    ];

    protected $scene = [
        'login' => ['username', 'password'],
        'refreshToken' => ['refresh_token']
    ];

}