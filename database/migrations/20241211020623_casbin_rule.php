<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CasbinRule extends Migrator
{
    public function up()
    {
        $table = $this->table('casbin_rule');
        $table->addColumn('ptype', 'string', ['null' => true])
            ->addColumn('v0', 'string', ['null' => true])
            ->addColumn('v1', 'string', ['null' => true])
            ->addColumn('v2', 'string', ['null' => true])
            ->addColumn('v3', 'string', ['null' => true])
            ->addColumn('v4', 'string', ['null' => true])
            ->addColumn('v5', 'string', ['null' => true])
            ->create();
    }

    public function down()
    {
        $table = $this->table('casbin_rule');
        $table->drop();
    }
}
