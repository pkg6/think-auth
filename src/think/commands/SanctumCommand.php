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
use tp5er\think\auth\sanctum\TransientToken;
use tp5er\think\auth\User;

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

        $user = User::find(1);
        foreach ($user->tokens as $token) {
            $output->info("已颁发令牌信息：" . $token);
        }
        $token = $user->createToken('token-can', ['server:update']);
        $output->info("颁发令牌权限：" . json_encode($token));

        $output->info("验证权限（使用withAccessToken进行携带) 默认：" . TransientToken::class);
        $user->withAccessToken(new TransientToken);
        if ($user->tokenCan('server:update')) {
            $output->info("有权限");
        } else {
            $output->error("无权限");
        }
        $output->info("Revoke all tokens");
        $user->tokens()->delete();

        // Revoke a specific token...
        //$user->tokens()->where('id', $id)->delete();

        $output->info("根据ID完成一次登录");
        \auth()->loginUsingId(1);
        $user = \auth()->guard("sanctum")->user();
        $output->info("使用sanctum获取用户信息：" . json_encode($user));
    }
}
