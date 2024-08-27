<?php

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