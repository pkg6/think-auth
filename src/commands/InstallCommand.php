<?php

namespace tp5er\think\auth\commands;

use DateTime;
use DateTimeZone;
use Phinx\Util\Util;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use tp5er\think\auth\database\migrations\ModelHasPermission;
use tp5er\think\auth\database\migrations\ModelHasRole;
use tp5er\think\auth\database\migrations\Permission;
use tp5er\think\auth\database\migrations\PersonalAccessToken;
use tp5er\think\auth\database\migrations\Role;
use tp5er\think\auth\database\migrations\RoleHasPermission;
use tp5er\think\auth\database\migrations\User;
use tp5er\think\auth\support\Str;

class InstallCommand extends Command
{

    protected $migrationsClass = [
        ModelHasPermission::class,
        ModelHasRole::class,
        Permission::class,
        PersonalAccessToken::class,
        Role::class,
        RoleHasPermission::class,
        User::class,
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

    protected function execute(Input $input, Output $output)
    {
        if (!class_exists(\think\migration\Migrator::class)) {
            $output->error("Please install `topthink/think-migration`");
            return;
        }
        $this->migrations($output);
    }

    /**
     * @see \think\migration\Creator
     * @see Util::mapClassNameToFileName
     */
    protected function migrations(Output $output)
    {

        $path = $this->app->getRootPath() . 'database' . DIRECTORY_SEPARATOR . 'migrations';
        if (!file_exists($path)) {
            mkdir($path, 0775, true);
        }
        $inFiles = [];
        $glob    = new \FilesystemIterator($path, \FilesystemIterator::KEY_AS_FILENAME);
        /* @var \SplFileInfo $file */
        foreach ($glob as $file) {
            if ($file->isFile()) {
                $inFiles[] = $file->getBasename();
            }
        }
        $fn = function ($name) use (&$inFiles) {
            foreach ($inFiles as $file) {
                if (Str::endsWith($file, $name)) {
                    return $file;
                }
            }
            return false;
        };
        $dt = new DateTime('now', new DateTimeZone('UTC'));
        foreach ($this->migrationsClass as $i => $class) {
            if (class_exists($class)) {
                $name = Str::snake(class_basename($class)) . ".php";
                if (($tpMigrationName = $fn($name)) != false) {
                    $output->warning("file {$tpMigrationName} already exist");
                    continue;
                }
                $fileName = (int)$dt->format('YmdHis') + $i . '_' . $name;
                $ref      = new \ReflectionClass($class);
                $content  = str_replace(
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
