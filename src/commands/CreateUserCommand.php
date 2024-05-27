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

class CreateUserCommand extends Command
{
    /**
     * @return void
     */
    protected function configure()
    {
        // 指令配置
        $this->setName('auth:create-user')
            ->addArgument("user", Argument::OPTIONAL, "Username used to log in", "tp5er")
            ->addArgument("password", Argument::OPTIONAL, "Password used to log in", "123456")
            ->addArgument("table", Argument::OPTIONAL, "Custom user table name", "user")
            ->setDescription('think-auth Create an account and password');
    }

    /**
     * @param Input $input
     * @param Output $output
     *
     * @return int|void|null
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function execute(Input $input, Output $output)
    {
        $table = $input->getArgument('table');
        $user = $input->getArgument('user');
        $password = $input->getArgument('password');

        $first = $this->app->db->name($table)
            ->where(['username' => $user])
            ->find();
        if (is_null($first)) {
            $this->app->db->name($table)->insert([
                'username' => $user,
                "password" => hash_make($password)
            ]);
        } else {
            $password = '***';
            $describe = " (已存在，密码为加密后数据)";
        }
        $output->info(sprintf("创建用户成功,用户名：%s 密码：%s %s", $user, $password, $describe ?? ""));
    }
}
