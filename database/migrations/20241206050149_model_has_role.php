<?php

use think\migration\Migrator;
use think\migration\db\Column;

class ModelHasRole extends Migrator
{
    public function change()
    {
        $table = $this->table('model_has_roles', ['id' => false]);
        $table->addColumn('role_id', 'integer', ['null' => false])
            ->addColumn('model_type', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('model_id', 'integer', ['null' => false])
            ->addIndex(['role_id', 'model_type', 'model_id'], ['name' => 'model_has_roles_model_id_model_type_index'])
            ->save();
    }
}
