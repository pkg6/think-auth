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

use DateTime;
use DateTimeZone;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use tp5er\think\auth\database\migrations\PersonalAccessToken;
use tp5er\think\auth\database\migrations\User;
use tp5er\think\auth\database\seeder\UserSender;
use tp5er\think\auth\support\File;
use tp5er\think\auth\support\Str;

class InstallCommand extends Command
{
    /**
     * @var string[]
     */
    protected $migrationsClass = [
        PersonalAccessToken::class,
        User::class,
    ];

    /**
     * @var \class-string[]
     */
    protected $senderClass = [
        UserSender::class,
    ];

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
        $this->migrations($output);
        $this->sender($output);
        $this->app->console->call('migrate:run');
//        $this->app->console->call('seed:run');
    }

    /**
     * migrations 文件迁移.
     *
     * @see \think\migration\Creator
     * @see Util::mapClassNameToFileName
     */
    protected function migrations(Output $output)
    {
        $path = $this->app->getRootPath() . 'database' . DIRECTORY_SEPARATOR . 'migrations';
        if ( ! file_exists($path)) {
            mkdir($path, 0775, true);
        }
        $fileIterator = File::fileIterator($path);
        $dt = new DateTime('now', new DateTimeZone('UTC'));
        foreach ($this->migrationsClass as $i => $class) {
            if (class_exists($class)) {
                $name = Str::snake(class_basename($class)) . ".php";
                if (($tpMigrationName = Str::filesystemIteratorHGName($fileIterator, $name))) {
                    $output->warning("file {$tpMigrationName} already exist");
                    continue;
                }
                $fileName = (int) $dt->format('YmdHis') + $i . '_' . $name;
                $ref = new \ReflectionClass($class);
                $content = str_replace(
                    [sprintf('namespace %s;' . PHP_EOL, $ref->getNamespaceName())],
                    [''],
                    file_get_contents($ref->getFileName())
                );
                file_put_contents($path . DIRECTORY_SEPARATOR . $fileName, $content);
                $output->info("Migration of {$class} file completed. {$fileName}");
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
        $path = $this->app->getRootPath() . 'database' . DIRECTORY_SEPARATOR . 'seeds';
        if ( ! file_exists($path)) {
            mkdir($path, 0775, true);
        }
        $fileIterator = File::fileIterator($path);
        foreach ($this->senderClass as $i => $class) {
            if (class_exists($class)) {
                $name = class_basename($class) . ".php";
                if (($tpSenderName = Str::filesystemIteratorHGName($fileIterator, $name))) {
                    $output->warning("file {$tpSenderName} already exist");
                    continue;
                }
                $ref = new \ReflectionClass($class);
                $fileName = $name;
                $content = str_replace(
                    [sprintf('namespace %s;' . PHP_EOL, $ref->getNamespaceName())],
                    [''],
                    file_get_contents($ref->getFileName())
                );
                file_put_contents($path . DIRECTORY_SEPARATOR . $fileName, $content);
                $output->info("Migration of {$class} file completed. {$fileName}");
            }
        }
    }
}
