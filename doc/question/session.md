### 使其他设备上的 session 失效

 你应该确保 `/app/middleware.php` 的全局中间件组中存在 `\think\middleware\SessionInit::class` 中间件，并且没有被注释掉：

 ~~~
 <?php
 // 全局中间件定义文件
 return [
     // Session初始化
      \think\middleware\SessionInit::class
 ];

 ~~~

