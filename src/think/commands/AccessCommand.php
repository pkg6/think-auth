<?php

namespace tp5er\think\auth\think\commands;


use think\console\Command;
use think\console\Input;
use think\console\Output;
use tp5er\think\auth\contracts\Authenticatable;
use tp5er\think\auth\facade\Gate;


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

        if (Gate::allows('edit-settings')) {
            $output->info("有权限");
        } else {
            $output->error("没有权限");
        }

    }
}