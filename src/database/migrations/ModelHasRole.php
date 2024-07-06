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
