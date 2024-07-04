<?php

namespace tp5er\think\auth\database\migrations;

use think\migration\Migrator;

class RoleHasPermission extends Migrator
{
    public function change()
    {
        $table = $this->table('model_has_permission', ['id' => false]);
        $table->addColumn('permission_id', 'integer', ['null' => false])
            ->addColumn('role_id', 'integer', ['null' => false])
            ->addIndex(['role_id'], ['name' => 'role_has_permissions_role_id_foreign'])
            ->save();
    }
}