<?php

use think\migration\Migrator;
use think\migration\db\Column;

class User extends Migrator
{
    public function change()
    {
        $table = $this->table('user');
        $table->addColumn('name', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('email', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('email_verified_at', 'timestamp', ['null' => true])
            ->addColumn('password', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('remember_token', 'string', ['limit' => 100, 'null' => true])
            ->addTimestamps()
            ->addIndex(['email'], ['unique' => true,'name' => 'users_email_unique'])
            ->create();
    }
}
