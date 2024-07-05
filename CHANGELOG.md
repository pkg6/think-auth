# 0.1.8

运行`php think auth:key-generate` 生成 JWT 密钥

# 0.1.7

标记deprecated func

# 0.1.6

feat: JWT

update composer.json

feat: Register

feat: fireLogoutEvent

feat: auth request

feat: Macroable

remove thinkphp

remove 无效代码

# 0.1.5

fix: sessiongrard rehashUserPassword

fix: session && model provider

# 0.1.4

docs: Update README.md

# 0.1.3

docs: packagist ioc

feat: providers.driver=model

docs: config auth

# 0.1.2

添加测试脚本命令需手动添加\tp5er\think\auth\think\Service::class

- 测试准备初始化成功php think auth:test-init
- auth方法测试 `php think auth:test-auth`
- access方法测试 `php think auth:test-access`
- sanctum方法测试 `php think auth:test-sanctum`

通过路由方式进行测试`\tp5er\think\auth\think\Route::api();`

# 0.1.1

feat:sanctum

fix: policies

test: policies

# 0.1.0

测试版本think-auth发布


