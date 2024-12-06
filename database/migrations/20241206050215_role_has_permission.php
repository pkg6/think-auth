<?php

use think\migration\Migrator;
use think\migration\db\Column;

class RoleHasPermission extends Migrator
{
    public function change()
    {
        $table = $this->table('role_has_permissions', ['id' => false]);
        $table
            ->addColumn('permission_id', 'integer', ['null' => false])
            ->addColumn('role_id', 'integer', ['null' => false])
            ->addIndex(['role_id'], ['name' => 'role_has_permissions_role_id_foreign'])
            ->save();
    }
}
