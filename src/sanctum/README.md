## 手动注册服务`app/service.php`

~~~
<?php

use app\AppService;

// 系统服务定义文件
// 服务在完成全局初始化之后执行
return [
   	..............
    \tp5er\think\auth\sanctum\Service::class,
];

~~~

## API 令牌认证

### 颁发 API 令牌

Sanctum 允许我们颁发 API 令牌或者个人访问令牌用于认证 API 请求，当使用 API 令牌发起请求时，该令牌应该以 Bearer 令牌形式包含在 Authorization 请求头中。

在为用户颁发令牌之前，需要在 User 模型类中使用 HasApiTokens trait：

~~~
<?php

namespace tp5er\think\auth;

use think\Model;
use tp5er\think\auth\Contracts\Authenticatable as AuthenticatableContract;
use tp5er\think\auth\sanctum\HasApiTokens;

class User extends Model implements AuthenticatableContract
{
    use Authenticatable,HasApiTokens;
}
~~~

要颁发令牌，可以使用 `createToken` 方法，该方法会返回一个 `tp5er\think\auth\sanctum\NewAccessToken` 实例。API 令牌在存放到数据库之前会使用 SHA-256 算法进行哈希，不过我们可以使用 `NewAccessToken` 实例的 `plainTextToken` 属性来访问该令牌的纯文本格式值。你需要在令牌创建之后立即将其展示给用户：

~~~
$token = $user->createToken('token-name');
return $token->plainTextToken;
~~~

我们可以通过 HasApiTokens trait 提供的 tokens 关联关系访问用户的所有令牌：

```
foreach ($user->tokens as $token) {
    //
}
```

### 令牌权限

Sanctum 允许我们为令牌分配权限，该功能和 OAuth 的作用域类似。可以传递权限字符串数组作为 `createToken` 方法的第二个参数

~~~
return $user->createToken('token-name', ['server:update'])->plainTextToken;
~~~

在使用 Sanctum 处理输入请求认证时，你可以使用 tokenCan 方法判断令牌是否具备给定权限：

```php
if ($user->tokenCan('server:update')) {
    //
}
```

### 撤销令牌

你可以通过 HasApiTokens trait 提供的 tokens 关联关系删除数据库中的令牌来实现令牌「撤销」：

```php
// Revoke all tokens...
$user->tokens()->delete();

// Revoke a specific token...
$user->tokens()->where('id', $id)->delete();
```