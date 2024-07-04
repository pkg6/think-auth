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

class PermissionCommand extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('auth:test-permission')
            ->setDescription('think-auth test Permission');
    }
    protected function execute(Input $input, Output $output)
    {
        //TODO permission test
    }
}
