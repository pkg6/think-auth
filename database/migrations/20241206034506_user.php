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

use think\migration\Migrator;

class User extends Migrator
{
    public function up()
    {
        $table = $this->table('user');
        $table->addColumn('name', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('email', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('email_verified_at', 'timestamp', ['null' => true])
            ->addColumn('password', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('remember_token', 'string', ['limit' => 100, 'null' => true])
            ->addTimestamps()
            ->addIndex(['email'], ['unique' => true,'name' => 'users_email_unique'])
            ->create();
    }
    public function down()
    {
        $table = $this->table('user');
        $table->drop();
    }
}
