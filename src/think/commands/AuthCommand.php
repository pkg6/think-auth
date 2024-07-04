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
use tp5er\think\auth\events\Login;
use tp5er\think\auth\User;
use function auth;

class AuthCommand extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('auth:test-auth')
            ->setDescription('think-auth test');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->comment("default 测试....");

        $this->app->event->listen(Login::class, function (Login $user) use (&$output) {
            $output->info("登录事件监听 获取用户信息 " . json_encode($user));
        });

        $database = auth();
        $database->attempt(["name" => "tp5er","password" => "123456"]);
        $output->info("默认测试：获取用户ID：" . $database->id());
        $output->info("默认测试：检查用户状态：" . $database->check());

        $output->comment("database 测试...");
        $database = auth()
            ->setConfigGuardProvider("database", 'user')
            ->guard('database');
        $database->attempt(["name" => "tp5er","password" => "123456"]);

        $output->info("database测试：获取用户ID：" . $database->id());
        $output->info("database测试：检查用户状态：" . $database->check());

        $output->comment("eloquent 测试...");
        $model = auth()
            ->setConfigGuardProvider("eloquent", User::class)
            ->guard('eloquent');
        $user = User::where(["name" => "tp5er"])
            ->find();
        $model->login($user, true);
        $output->info("eloquent测试：获取用户ID：" . $model->id());
        $output->info("eloquent测试：检查用户状态：" . $model->check());
    }
}
