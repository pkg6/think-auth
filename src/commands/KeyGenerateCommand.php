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
use think\console\Output;

class KeyGenerateCommand extends Command
{
    /**
     * @return void
     */
    protected function configure()
    {
        // 指令配置
        $this->setName('auth:key-generate')
            ->setDescription('Add jwt secret to the. env file');
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
    }
}
