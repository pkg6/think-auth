
## 基础user表
~~~
CREATE TABLE `user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名称',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '邮箱地址',
  `email_verified_at` timestamp NULL DEFAULT NULL COMMENT '邮箱验证时间',
  `phone` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '手机号',
  `phone_verified_at` timestamp NULL DEFAULT NULL COMMENT '手机验证时间',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '密码',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '记住密码token',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_email_unique` (`email`),
  UNIQUE KEY `user_phone_unique` (`phone`) USING HASH
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
~~~


>考虑已有的数据库字段密码字段就不是`password`那么解决方案来了
>
>```
>\tp5er\think\auth\Model\Field::$password='pwd';
>```

>在provider中使用`model`,需要做一下操作
>
>1. 创建User模型
>2. 模型继承 tp5er\think\auth\Model\User`

## Auth常用方法
~~~
//如果你愿意，除了用户的电子邮件和密码之外，还可以向身份验证查询中添加额外的查询条件。为了实现这一点，我们可以简单地将查询条件添加到传递给 attempt 方法的数组中。
Auth::attempt(['email' => 'tp5er@qq.com', 'password' => '123456'], true);

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
//访问特定的看守器实例
Auth::guard('api')->attempt($credentials);
Auth::guard('api')->login(User::find(1), $remember = false);
~~~

## JwtAuth常用方法

~~~
//登陆
$token = JwtAuth::attempt(['email' => 'tp5er@qq.com', 'password' => '123456']);
//设置token,在默认情况下首先是通过`request()->param('token')`方法或者 `request()->header('Authorization')` 进行获取token，如果不是以上方法，可以自行获取并设置
JwtAuth::setToken($token)
//获取Payload
$payload=JwtAuth::getPayload();
//当前的登陆id
$id=JwtAuth::id();
//当前登陆用户信息获取
$payload=JwtAuth::authenticate();
//刷新token
$newToken=JwtAuth::refresh();
//设置特定的看守器进行以上操作
JwtAuth::setAuth('api')->attempt(['email' => 'tp5er@qq.com', 'password' => '123456']);
~~~

## 常见问题

-  [密码始终验证不通过](doc/question/password.md)
-  [使其他设备上的 session 失效](doc/question/session.md)

## 加入我们

如果你认可我们的开源项目，有兴趣为 think-auth 的发展做贡献，竭诚欢迎加入我们一起开发完善。无论是[报告错误](https://github.com/tp5er/think-auth/issues)或是 Pull Request 开发，那怕是修改一个错别字也是对我们莫大的帮助。



## 许可协议

[MIT](https://opensource.org/licenses/MIT)

