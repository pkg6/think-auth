[![Latest Stable Version](http://poser.pugx.org/tp5er/think-auth/v)](https://packagist.org/packages/tp5er/think-auth) [![Total Downloads](http://poser.pugx.org/tp5er/think-auth/downloads)](https://packagist.org/packages/tp5er/think-auth) [![Latest Unstable Version](http://poser.pugx.org/tp5er/think-auth/v/unstable)](https://packagist.org/packages/tp5er/think-auth) [![License](http://poser.pugx.org/tp5er/think-auth/license)](https://packagist.org/packages/tp5er/think-auth) [![PHP Version Require](http://poser.pugx.org/tp5er/think-auth/require/php)](https://packagist.org/packages/tp5er/think-auth)

## 介绍

许多web应用程序为用户提供了一种通过应用程序进行身份验证和“登录”的方式。在web应用程序中实现此功能可能是一项复杂且潜在风险的工作。因此，think-auth努力为您提供快速、安全、轻松地实现身份验证所需的工具。
其核心是，think-auth的认证设施由“卫士”和“提供者”组成。防护程序定义了如何对每个请求的用户进行身份验证。例如，think-auth附带了一个会话保护程序，该程序使用会话存储和cookie来维护状态。
提供程序定义如何从持久存储中检索用户。think-auth提供了使用Eloquent和数据库查询生成器检索用户的支持。但是，您可以根据应用程序的需要自由定义其他提供程序。
您的应用程序的身份验证配置文件位于config/auth.php中。该文件包含几个详细记录的选项，用于调整think-auth的身份验证服务的行为。

## 安装

~~~
composer require tp5er/think-auth
~~~

## 版本更新记录

[CHANGELOG.md](https://github.com/pkg6/think-auth/blob/main/CHANGELOG.md)

## 命令行

~~~
//生成基础用户表，如果重命名，需要继承\tp5er\think\auth\User,然后修改`config/auth.php`中的providers
php think auth:create-user

//生成基础personal_access_token表,如果重写命名 需继承\tp5er\think\auth\sanctum\PersonalAccessToken
//修改模型地址 \tp5er\think\auth\sanctum\Sanctum::$personalAccessTokenModel =\app\model\PersonalAccessToken::class;
php think auth:migrate-access-token

//创建一个admin用户，密码为123456
php think auth:create-user  admin 123456

//指定用户表中创建一个admin用户，密码为123456
php think auth:create-user  admin 123456 user

// 使用policy类
php think make:policy Post
~~~

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
Auth::configMergeGuards('sanctum', ["driver" => 'sanctum',"provider" => null])
Auth::configMergeProviders("admin", ['driver' => 'database','table' => "user"]);
~~~

## 使用policy

生成Post模型

~~~
php think make:model Post
~~~

#### 生成一个PostPolicy

~~~
php think make:policy Post
~~~

#### 加入配置`config/auth.php`

~~~
"policies" => [
    //'app\model\Model' => 'app\policies\ModelPolicy',
    \app\model\Post::class => \app\policies\Post::class,
],
~~~

## 使用

~~~
use tp5er\think\auth\access\AuthorizesRequests

public function destroy(Post $post)
{
    $this->authorize('delete', $post);
    $post->delete();
    return redirect('/posts');
}
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

## 在路由演示使用think-auth

> 根据实际需求进行开发使用

在`route/app.php`添加

~~~
\tp5er\think\auth\think\Route::api();
~~~

部分代码(此处只是部分代码,演示有可能随时发生变化,但使用方法是不会发生变化)

~~~
use think\facade\Route as thinkRoute;
use tp5er\think\auth\contracts\Authenticatable;
use tp5er\think\auth\facade\Gate;
use tp5er\think\auth\User;

//定义一个演示的权限
Gate::define('edit-settings', function (Authenticatable $authenticatable) {
    return true;
});

thinkRoute::get("/api/register", function () {
    //TODO 自己根据实际需求进行注册
    $user = new User();
    $user->username = "tp5er";
    $user->password = hash_make("123456");
    $user->save();

    return json(['code' => 0, "msge" => $user]);
});

thinkRoute::get("/api/login", function () {
    //TODO 自己根据实际需求进行登录
    auth()->attempt(["username" => "tp5er", "password" => "123456"]);

    return json(['code' => 0, "msge" => "登录成功"]);
});
thinkRoute::get("/api/user", function () {

    $user = requestUser();
    //$user=  auth()->user();

    return json(['code' => 0, "msg" => "获取登录信息", "data" => $user]);
});

thinkRoute::get("/api/scan", function () {

    $ret = [];
    if (Gate::allows('edit-settings')) {
        $ret["edit-settings"] = "有权限";
    } else {
        $ret["edit-settings"] = "无权限";
    }

    if (Gate::allows('delete-settings')) {
        $ret["delete-settings"] = "有权限";
    } else {
        $ret["delete-settings"] = "无权限";
    }

    return json(['code' => 0, "msg" => "获取权限列表", 'data' => $ret]);

});

thinkRoute::get("/api/token", function () {
    //$user = requestUser();
    $user = auth()->user();
    $token = $user->createToken("test-token");

    return json(['code' => 0, "msg" => "获取token信息", "data" => ["token" => $token->plainTextToken]]);
});

thinkRoute::get("/api/sanctum", function () {
    //TODO 逻辑
    // 1. 首先判断你是否完成登录，通过默认guard中获取用户信息，如果有用户进行就直接返回
    // 2. 如果在默认的guard没有获取到用户信息就通过header中获取Authorization，然后进行获取用户信息
    // 3. Authorization是用`/api/token`中拿到的token，然后进字符串拼接成：（Bearer token）放在header中Bearer 参考curl
    // curl -H "Authorization: Bearer 9|DqTQsBngTVJcFwJkslyvdZSeGuAjgaeikknQPHBI"  "http://127.0.0.1:8000/api/sanctum"
    // 注意： 使用sanctum必须使用模型，database 无法进行access权限验证

    //$user = requestUser();
    $user = auth()->user();

    return json(['code' => 0, "msg" => "通过sanctum获取用户信息", "data" => $user]);
})->middleware('auth', "sanctum");

thinkRoute::get("/api/tokencan", function () {
    //$user = requestUser();
    $user = auth()->user();
    $ret = [];
    //TODO 默认accessToken是tp5er\think\auth\sanctum\TransientToken
    // 此处无论是什么都有权限的哦
    // 可以使用withAccessToken(HasAbilities $accessToken) 进行自定义
    if ($user->tokenCan("edit-settings")) {
        $ret["tokenCan"] = "有权限";
    } else {
        $ret["tokenCan"] = "无权限";
    }
    //TODO Gate 定义的关系
    if ($user->can("edit-settings")) {
        $ret["edit-settings"] = "有权限";
    } else {
        $ret["edit-settings"] = "无权限";
    }
    if ($user->can('delete-settings')) {
        $ret["delete-settings"] = "有权限";
    } else {
        $ret["delete-settings"] = "无权限";
    }

    return json(['code' => 0, "msg" => "获取权限列表", 'data' => $ret]);

})->middleware('auth', "sanctum");
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

## 感谢

https://laravel.com/docs/8.x/authentication

https://github.com/laravel/framework/tree/8.x/src/Illuminate/Auth

https://github.com/laravel/sanctum

https://github.com/tymondesigns/jwt-auth

## 许可协议

[MIT](https://opensource.org/licenses/MIT)

