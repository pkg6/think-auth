<?php

use think\migration\Migrator;
use think\migration\db\Column;

class ModelHasPermission extends Migrator
{
    public function change()
    {
        $table = $this->table('model_has_permission', ['id' => false]);
        $table->addColumn('permission_id', 'integer', ['null' => false])
            ->addColumn('model_type', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('model_id', 'integer', ['null' => false])
            ->addIndex(['permission_id', 'model_type', 'model_id'], ['name' => 'model_has_permissions_model_id_model_type_index'])
            ->save();
    }
}
