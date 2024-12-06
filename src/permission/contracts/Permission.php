<?php

namespace tp5er\think\auth\permission\contracts;

use think\model\relation\BelongsToMany;

interface Permission
{
    public function roles(): BelongsToMany;
    public static function findByName(string $name, $guardName);
    public static function findById(int $id, $guardName);

    public static function findOrCreate(string $name, $guardName);
}