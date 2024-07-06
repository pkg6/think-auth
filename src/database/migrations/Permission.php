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

class Permission extends Migrator
{
    public function change()
    {
        $table = $this->table('permission');
        $table->addColumn('name', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('guard_name', 'string', ['limit' => 255, 'null' => false])
            ->addIndex(['name', 'guard_name'], ['unique' => true, 'name' => 'idx_name_guard_name'])
            ->addTimestamps()
            ->save();
    }
}
