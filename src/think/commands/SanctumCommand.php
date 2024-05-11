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
use tp5er\think\auth\sanctum\Service;

class SanctumCommand extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('auth:test-sanctum')
            ->setDescription('think-auth test sanctum');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->info("手动注册服务 " . Service::class);
        $output->info("根据ID完成一次登录");
        \auth()->loginUsingId(1);
        $user = \auth()->guard("sanctum")->user();
        $output->info("使用sanctum获取用户信息：" . json_encode($user));
    }
}
