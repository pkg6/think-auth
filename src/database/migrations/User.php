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

namespace tp5er\think\auth\database\migrations;

use think\migration\Migrator;

class User extends Migrator
{
    public function change()
    {
        $table = $this->table('user');
        $table->addColumn('name', 'string', ['limit' => 255, 'default' => ''])
            ->addColumn('email', 'string', ['limit' => 255, 'default' => ''])
            ->addColumn('email_verified_at', 'timestamp', ['null' => false, 'default' => 'CURRENT_TIMESTAMP', 'update' => '', 'timezone' => false])
            ->addColumn('password', 'string', ['limit' => 255, 'default' => ''])
            ->addColumn('remember_token', 'string', ['limit' => 100, 'default' => ''])
            ->addTimestamps()
            ->addIndex(['email'], ['unique' => true])
            ->create();
    }
}
