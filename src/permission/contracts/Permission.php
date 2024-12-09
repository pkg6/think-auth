<?php

namespace tp5er\think\auth\permission\contracts;

use think\model\relation\BelongsToMany;

interface Permission
{
    public function roles(): BelongsToMany;

    public static function findByName($name, $guardName);

    public static function findById($id, $guardName);

    public static function findOrCreate($name, $guardName);
}