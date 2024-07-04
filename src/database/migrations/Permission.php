<?php

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