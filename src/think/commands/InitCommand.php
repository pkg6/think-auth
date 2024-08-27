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
use tp5er\think\auth\commands\CreateUserCommand;
use tp5er\think\auth\commands\InstallCommand;
use tp5er\think\auth\commands\MigrateAccessTokenCommand;
use tp5er\think\auth\commands\MigrateUserCommand;

class InitCommand extends Command
{
    /**
     * @return void
     */
    protected function configure()
    {
        // 指令配置
        $this->setName('auth:test-init')
            ->setDescription('think-auth test init');
    }

    /**
     * @param Input $input
     * @param Output $output
     *
     * @return int|void|null
     */
    protected function execute(Input $input, Output $output)
    {

        $this->app->console->call((new InstallCommand())->getName());
        $this->app->console->call('migrate:run');
        $this->app->console->call('seed:run');
        $this->post_db_create();
        $output->info("测试准备初始化成功");
    }

    /**
     * @return void
     */
    protected function post_db_create()
    {
        $sql = 'CREATE TABLE `post` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100)  DEFAULT "",
  `title` varchar(50)  DEFAULT "",
  `content` varchar(255)  DEFAULT "",
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;';
        $this->app->db->execute($sql);
    }
}
