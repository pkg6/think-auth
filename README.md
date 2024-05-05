## 安装

~~~
composer require tp5er/think-auth
~~~

## 版本更新记录

[CHANGELOG.md](https://github.com/pkg6/think-auth/blob/main/CHANGELOG.md)

## 基础user表

~~~
CREATE TABLE `users`
(
    `id`             int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `username`       varchar(50)      DEFAULT '' COMMENT '用户名',
    `nickname`       varchar(50)      DEFAULT '' COMMENT '昵称',
    `email`          varchar(100)     DEFAULT '' COMMENT '电子邮箱',
    `mobile`         varchar(11)      DEFAULT '' COMMENT '手机号码',
    `password`       varchar(255)     DEFAULT '' COMMENT '密码',
    `remember_token` varchar(255)     DEFAULT '' COMMENT '记住token标识',
    PRIMARY KEY (`id`),
    UNIQUE KEY `username` (`username`) USING BTREE
) ENGINE=InnoDB CHARSET=utf8mb4  COMMENT='用户表';
~~~


>在provider中使用`model`,需要做一下操作
>
>1. 创建User模型
>2. 模型继承` tp5er\think\auth\User`

## Auth常用方法
~~~
//如果你愿意，除了用户的电子邮件和密码之外，还可以向身份验证查询中添加额外的查询条件。为了实现这一点，我们可以简单地将查询条件添加到传递给 attempt 方法的数组中。
Auth::attempt(['email' => 'zhiqiang2033@gmail.com', 'password' => '123456'], true);

//访问指定的看守器实例
if (Auth::guard('admin')->attempt($credentials)) {
    //
}

//您可以将布尔值作为第二个参数传递给 login 方法。此值指示是否需要验证会话的 「记住我」 功能。请记住，这意味着会话将被无限期地验证，或者直到用户手动注销应用程序：
Auth::login(User::find(1), $remember = false);

//只验证一次
Auth::once(['email' => 'tp5er@qq.com', 'password' => '123456']);
//只验证一次通过id
Auth::onceUsingId(1);

// 获取当前的认证用户信息 ...
$user = Auth::user();
// 获取当前的认证用户id ...
$id = Auth::id();

if (Auth::check()) {
    // 用户已登录...
}
//使用户退出登录（清除会话）
Auth::logout();

//注册任意应用身份验证 / 授权服务
Auth::provider('riak', function ($app, array $config) {
      // 返回实现 tp5er\think\auth\contracts\StatefulGuard 对象
});


//添加自定义的看守器
Auth::extend("test",function (App $app, $name, $config){
    //返回实现Guard|StatefulGuard的对象

});
//添加自定义用户提供器
Auth::provider("test",function (App $app,$config){
    //返回实现UserProvider的对象
});


//动态设置配置信息
Auth::setConfigGuardProvider("admin","user_table","session");
~~~

## 使用事件

### 定义事件类LoginSuccessful

~~~
<?php
declare (strict_types = 1);

namespace app\event;

use tp5er\think\auth\events\Login;

class LoginSuccessful
{
    public function handle($user $authenticated)
    {
        //TODO 登录成功执行
    }
}
~~~

### 绑定事件

~~~
'listen'    => [
    \tp5er\think\auth\events\Attempting::class=> [
        \app\event\LoginSuccessful::class
    ],
    \tp5er\think\auth\events\Authenticated::class=>[],
    \tp5er\think\auth\events\CurrentDeviceLogout::class=>[],
    \tp5er\think\auth\events\Failed::class=>[],
    \tp5er\think\auth\events\Login::class=>[],
    \tp5er\think\auth\events\Logout::class=>[],
    \tp5er\think\auth\events\OtherDeviceLogout::class=>[],
],
~~~

### 在控制器中直接绑定事件

~~~
app()->event->listen( Authenticated::class,function (Authenticated $user){
    //TODO
});

Auth::loginUsingId(1);
~~~

## [密码生成和验证](https://github.com/pkg6/think-hashing)

~~~
use tp5er\think\hashing\facade\Hash;

//加密
$hashedValue= Hash::make("123456");
//验证密码是否有效
$check = Hash::check("123456",$hashedValue);

//加密
$hashedValue = hash_make("123456");
//验证密码是否有效
hash_check("123456",$hashedValue);
~~~

## 加入我们

如果你认可我们的开源项目，有兴趣为 think-auth 的发展做贡献，竭诚欢迎加入我们一起开发完善。无论是[报告错误](https://github.com/pkg6/think-auth/issues)或是 [Pull Request](https://github.com/pkg6/think-hashing/pulls) 开发，那怕是修改一个错别字也是对我们莫大的帮助。

## 许可协议

[MIT](https://opensource.org/licenses/MIT)

