<?php

namespace tp5er\think\auth\database\migrations;

use think\migration\Migrator;

class Role extends Migrator
{
    public function change()
    {
        $table = $this->table('role');
        $table->addColumn('name', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('guard_name', 'string', ['limit' => 255, 'null' => false])
            ->addIndex(['name', 'guard_name'], ['unique' => true, 'name' => 'roles_name_guard_name_unique'])
            ->addTimestamps()
            ->save();
    }
}