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
use tp5er\think\auth\support\File;

class InstallCommand extends Command
{
    /**
     * @return void
     */
    protected function configure()
    {
        // 指令配置
        $this->setName('auth:install')
            ->setDescription('think-auth install');
    }

    /**
     * @param Output $output
     *
     * @return bool
     */
    protected function check(Output $output)
    {
        if ( ! class_exists(\think\migration\Migrator::class)) {
            $output->error("Please install `topthink/think-migration`");

            return false;
        }

        return true;
    }

    /**
     * @param Input $input
     * @param Output $output
     *
     * @return void
     */
    protected function execute(Input $input, Output $output)
    {
        $check = $this->check($output);
        if ( ! $check) {
            return;
        }
        $this->cashbin($output);
        $this->migrations($output);
        $this->sender($output);
        $output->highlight("Manually execute as needed：");
        $output->comment("php think migrate:run");
        $output->comment("php think seed:run");
        //$this->app->console->call('migrate:run');
        //$this->app->console->call('seed:run');
    }

    /**
     * migrations 文件迁移.
     *
     * @see \think\migration\Creator
     * @see Util::mapClassNameToFileName
     */
    protected function migrations(Output $output)
    {
        $targetPath = $this->app->getRootPath() . 'database' . DIRECTORY_SEPARATOR . 'migrations';
        $sourcePath = __DIR__ . '/../../database/migrations';
        $files = File::publishes($sourcePath, $targetPath);
        foreach ($files as $file) {
            [$b, $sourceFile, $targetFile] = $file;
            if ($b) {
                $output->info("【Migrations】Successfully transitioned from {$sourceFile} cp to {$targetFile}");
            } else {
                $output->error("【Migrations】Failed from {$sourceFile} cp to {$targetFile}");
            }
        }
    }

    /**
     * @param Output $output
     *
     * @return void
     */
    public function sender(Output $output)
    {
        $targetPath = $this->app->getRootPath() . 'database' . DIRECTORY_SEPARATOR . 'seeds';
        $sourcePath = __DIR__ . '/../../database/seeder';
        $files = File::publishes($sourcePath, $targetPath);
        foreach ($files as $file) {
            [$b, $sourceFile, $targetFile] = $file;
            if ($b) {
                $output->info("【Seeder】Successfully transitioned from {$sourceFile} cp to {$targetFile}");
            } else {
                $output->error("【Seeder】Failed from {$sourceFile} cp to {$targetFile}");
            }
        }
    }

    public function cashbin(Output $output)
    {
        $output->info("【casbin】 initialization");
        $fileName = 'casbin-basic-model.conf';
        if ( ! file_exists(root_path() . $fileName)) {
            copy(__DIR__ . '/../../config/' . $fileName, root_path() . $fileName);
        }
    }
}
