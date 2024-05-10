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

namespace tp5er\think\auth\commands;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;

class MigrateCommand extends Command
{

    protected function configure()
    {
        // 指令配置
        $this->setName('auth:migrate')
            ->addArgument("user", Argument::OPTIONAL, "Custom user table name", "user")
            ->setDescription('think-auth Create Table Structure');
    }

    protected function stubs()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR;
    }

    protected function execute(Input $input, Output $output)
    {
        $user_table = $input->getArgument("user");
        $user_sql = str_replace(['{%table%}'], [$user_table], file_get_contents($this->stubs() . 'sql_user.stub'));

        $this->app->db->execute($user_sql);
        $output->info(sprintf("【%s】创建成功", $user_table));
    }
}
