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
use tp5er\think\auth\contracts\Authenticatable;
use tp5er\think\auth\facade\Gate;
use tp5er\think\auth\think\model\Post;
use function auth;

class AccessCommand extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('auth:test-access')
            ->setDescription('think-auth test access');
    }

    protected function execute(Input $input, Output $output)
    {

        // 最简单的
        Gate::define('edit-settings', function (Authenticatable $authenticatable) {
            return true;
        });

        auth()->loginUsingId(1);

        $user = auth()->user();

        if ($user->can("edit-settings")) {
            $output->info("model 有权限");
        } else {
            $output->error("model 没有权限");
        }
        if (Gate::allows('edit-settings')) {
            $output->info("有权限");
        } else {
            $output->error("没有权限");
        }

        $post = new Post();
        if (\gate()->authorize('create', $post)) {
            $output->info("有权限");
        } else {
            $output->error("没有权限");
        }
    }
}
