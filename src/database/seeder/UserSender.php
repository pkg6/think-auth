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

namespace tp5er\think\auth\database\seeder;

use think\migration\Seeder;

class UserSender extends Seeder
{
    public function run(): void
    {
        $this->table('user')->insert([
            [
                'name' => "admin",
                'email' => 'zhiqiang2033@gmail.com',
                'password' => hash_make("admin"),
            ]
        ])->saveData();
    }
}
