### 密码始终验证不通过

可以使用`Password`进行加密验证，默认采用的`password_hash`和`password_verify`进行加密验证，如果需要自己生成可以自己实现

```
use tp5er\think\auth\Facades\Password;
//加密
Password::encrypt($password);
//验证
Password::verify(Authenticatable $user, string $password)
```

> 以md5的为例

1. 创建一个md5类去实现PasswordInterface中的encrypt和verify方法

 ~~~
 namespace tp5er\think\auth\Password;
 use tp5er\think\auth\Contracts\Authenticatable;
 class Md5 implements PasswordInterface
 {
  /**
      * @param Authenticatable $user
      * @param string $password
      * @return bool
      */
     public function verify(Authenticatable $user, string $password)
     {
         return md5($password) === $user->getAuthPassword();
     }

     /**
      * 加密
      * @param string $password
      * @return string
      */
     public function encrypt(string $password)
     {
         return md5($password);
     }
 }
 ~~~

2. 修改配置文件 

 ~~~
<?php
return [
.............
// 自定义密码验证
'password' => \tp5er\think\auth\Password\Md5::class,
.............
];
 ~~~

