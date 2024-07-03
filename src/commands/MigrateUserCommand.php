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

use tp5er\think\auth\User;

class MigrateUserCommand extends Migrate
{
    /**
     * @var string
     */
    protected $tableName = "user";
    /**
     * @var string
     */
    protected $model = User::class;
    /**
     * @var string
     */
    protected $cmdName = 'auth:migrate-user';

    /**
     * @return string
     */
    protected function createTableSQLTemp()
    {
        return file_get_contents(
            __DIR__ . DIRECTORY_SEPARATOR
            . 'stubs' . DIRECTORY_SEPARATOR
            . 'sql_user.stub'
        );
    }
}
