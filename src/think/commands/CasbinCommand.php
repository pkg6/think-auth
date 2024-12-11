<?php

namespace tp5er\think\auth\think\commands;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use tp5er\think\auth\facade\Casbin;

class CasbinCommand extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('auth:test-casbin')
            ->setDescription('think-auth test casbin');
    }

    protected function execute(Input $input, Output $output)
    {
        //https://casbin.org/docs/category/the-basics
        // 给用户alice赋予对data1的read权限
        Casbin::addPolicy('alice', 'data1', 'read');
        //判断是权限策略是否存在
        if (Casbin::hasPolicy('alice', 'data1', 'read')){
            $output->info("alice 有权限");
        }
        Casbin::removePolicy('alice', 'data1', 'read');
        $sub = 'alice'; // the user that wants to access a resource.
        $obj = 'data1'; // the resource that is going to be accessed.
        $act = 'read'; // the operation that the user performs on the resource.
        if (true === Casbin::enforce($sub, $obj, $act)) {
            // permit alice to read data1
            echo 'permit alice to read data1';
        } else {
            // deny the request, show an error
        }
    }
}