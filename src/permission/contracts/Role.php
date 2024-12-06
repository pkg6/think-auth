<?php

namespace tp5er\think\auth\permission\contracts;

use think\model\relation\BelongsToMany;

interface Role
{
    public function permissions(): BelongsToMany;
    public static function findByName(string $name, $guardName): self;
    public static function findById(int $id, $guardName): self;
    public static function findOrCreate(string $name, $guardName): self;
    public function hasPermissionTo($permission): bool;
}