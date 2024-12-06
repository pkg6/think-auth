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

use think\migration\Migrator;

class PersonalAccessToken extends Migrator
{
    public function change()
    {
        $table = $this->table('personal_access_token');
        $table->addColumn('tokenable_type', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('tokenable_id', 'biginteger', ['null' => false])
            ->addColumn('name', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('token', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('abilities', 'text')
            ->addColumn('last_used_at', 'timestamp', ['null' => true])
            ->addTimestamps()
            ->addIndex(['token'], ['unique' => true, 'name' => 'personal_access_tokens_token_unique'])
            ->addIndex(['tokenable_type', 'tokenable_id'], ['name' => 'personal_access_tokens_tokenable_type_tokenable_id_index'])
            ->create();
    }
}
