<?php

namespace tp5er\think\auth\Http\Validates;

use think\Validate;

class UserValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'=>['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'name'     => ['require'],
        'email'    => ['require', 'email'],
        'phone'    => ['require', 'mobile'],
        'password' => 'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'=>'错误信息'
     *
     * @var array
     */
    protected $message = [
        'name.require'     => '请输入管理员账号',
        'email.require'    => '请填写邮箱',
        'email.email'      => '必须是邮箱格式',
        'phone.require'    => '填写手机号',
        'phone.mobile'     => '必须是手机号格式',
        'password.require' => '请输入密码',
    ];

    protected $scene = [
        'login'    => ['name', 'password'],
        'register' => ['name', 'email', 'phone', 'password'],
    ];
}