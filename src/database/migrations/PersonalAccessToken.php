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

class PersonalAccessToken extends Migrator
{
    public function change()
    {
        $table = $this->table('personal_access_token');
        $table->addColumn('tokenable_type', 'string', ['limit' => 255, 'default' => ''])
            ->addColumn('tokenable_id', 'biginteger', ['default' => 0])
            ->addColumn('name', 'string', ['limit' => 255, 'default' => ''])
            ->addColumn('token', 'string', ['limit' => 255, 'default' => ''])
            ->addColumn('abilities', 'text')
            ->addColumn('last_used_at', 'timestamp', ['null' => false, 'default' => 'CURRENT_TIMESTAMP', 'update' => '', 'timezone' => false])
            ->addTimestamps()
            ->addIndex(['token'], ['unique' => true])
            ->addIndex(['tokenable_type','tokenable_id'])
            ->create();
    }
}
