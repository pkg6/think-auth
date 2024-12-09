<?php

namespace tp5er\think\auth\permission\models;

use think\Model;
use think\model\relation\BelongsToMany;
use tp5er\think\auth\permission\exceptions\GuardDoesNotMatch;
use tp5er\think\auth\permission\exceptions\RoleAlreadyExists;
use tp5er\think\auth\permission\exceptions\RoleDoesNotExist;
use tp5er\think\auth\permission\Guard;
use tp5er\think\auth\permission\PermissionRegistrar;
use tp5er\think\auth\permission\traits\HasPermissions;


class Role extends Model implements \tp5er\think\auth\permission\contracts\Role
{

    use HasPermissions;

    protected $table = 'roles';


    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            $this->getPermissionClass(),
            app(PermissionRegistrar::class)->roleHasPermissionTable,
            app(PermissionRegistrar::class)->pivotPermission,
            app(PermissionRegistrar::class)->pivotRole
        );
    }

    public static function findByName(string $name, $guardName): \tp5er\think\auth\permission\contracts\Role
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $role = static::findByParam(['name' => $name, 'guard_name' => $guardName]);
        if (!$role) {
            throw RoleDoesNotExist::named($name);
        }
        return $role;
    }

    public static function findById(int $id, $guardName): \tp5er\think\auth\permission\contracts\Role
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $role = static::findByParam([(new static())->getPk() => $id, 'guard_name' => $guardName]);
        if (!$role) {
            throw RoleDoesNotExist::withId($id);
        }
        return $role;
    }

    public static function findOrCreate(string $name, $guardName): \tp5er\think\auth\permission\contracts\Role
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $role = static::findByParam(['name' => $name, 'guard_name' => $guardName]);
        if (!$role) {
            return static::creates(['name' => $name, 'guard_name' => $guardName]);
        }
        return $role;
    }

    public function hasPermissionTo($permission, $guardName = null): bool
    {
        $permissionClass = $this->getPermissionClass();
        if (is_string($permission)) {
            $permission = $permissionClass->findByName($permission, $this->getDefaultGuardName());
        }
        if (PermissionRegistrar::isUid($permission)) {
            $permission = $permissionClass->findById($permission, $this->getDefaultGuardName());
        }
        if (! $this->getGuardNames()->contains($permission->guard_name)) {
            throw GuardDoesNotMatch::create($permission->guard_name, $this->getGuardNames());
        }
        return $this->permissions->contains('id', $permission->id);
    }

    public static function creates($attributes)
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? Guard::getDefaultName(static::class);
        $params = ['name' => $attributes['name'], 'guard_name' => $attributes['guard_name']];
        if (static::findByParam($params)) {
            throw RoleAlreadyExists::create($attributes['name'], $attributes['guard_name']);
        }
        return static::create($attributes);
    }

    protected static function findByParam(array $params = [])
    {
        $query = (new Role)->newQuery();
        foreach ($params as $key => $value) {
            $query->where($key, $value);
        }
        return $query->find();
    }
}