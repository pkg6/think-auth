<?php

/*
 * This file is part of the tp5er/think-auth
 *
 * (c) pkg6 <https://github.com/pkg6>
 *
 * (L) Licensed <https://opensource.org/license/MIT>
 *
 * (A) zhiqiang <https://www.zhiqiang.wang>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace tp5er\think\auth\think\commands;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use tp5er\think\auth\support\Str;
use function auth;

class JWTCommand extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('auth:test-jwt')
            ->setDescription('think-auth test jwt');
    }
    protected function execute(Input $input, Output $output)
    {
        $token = auth('jwt')->attempt(["username" => "tp5er", "password" => "123456"]);
        $output->info("登录生成JWT-Token ：" . $token);
        $user = auth('jwt')->user();
        $output->info("获取用户信息 ：" . $user);
        //$newton = auth('jwt')->parseToken()->refresh();
        //$output->info("刷新之后的token ：" . $newtoken);
        auth('jwt')->logout();
        $output->info("退出登录");
    }
}
