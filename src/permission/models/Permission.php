<?php

namespace tp5er\think\auth\permission\models;

use think\Model;
use think\model\Collection;
use think\model\relation\BelongsToMany;
use tp5er\think\auth\permission\exceptions\PermissionAlreadyExists;
use tp5er\think\auth\permission\exceptions\PermissionDoesNotExist;
use tp5er\think\auth\permission\Guard;
use tp5er\think\auth\permission\PermissionRegistrar;
use tp5er\think\auth\permission\traits\HasRoles;


class Permission extends Model implements \tp5er\think\auth\permission\contracts\Permission
{
    use HasRoles;

    protected $table = 'permissions';

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            app(PermissionRegistrar::class)->modelClassRole,
            app(PermissionRegistrar::class)->roleHasPermissionTable,
            app(PermissionRegistrar::class)->pivotRole,
            app(PermissionRegistrar::class)->pivotPermission
        );
    }



    public static function creates(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? Guard::getDefaultName(static::class);
        $permission = static::getPermissions(['name' => $attributes['name'], 'guard_name' => $attributes['guard_name']]);
        if ($permission) {
            throw PermissionAlreadyExists::create($attributes['name'], $attributes['guard_name']);
        }
        return static::create($attributes);
    }

    public static function findByName($name, $guardName)
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $permission = static::getPermissions(['name' => $name, 'guard_name' => $guardName]);
        if (!$permission) {
            throw PermissionDoesNotExist::create($name, $guardName);
        }
        return $permission;
    }

    public static function findById($id, $guardName)
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $permission = static::getPermissions([(new static())->getPk() => $id, 'guard_name' => $guardName]);
        if (!$permission) {
            throw PermissionDoesNotExist::withId($id, $guardName);
        }
        return $permission;
    }

    public static function findOrCreate($name, $guardName)
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $permission = static::getPermissions(['name' => $name, 'guard_name' => $guardName]);
        if (!$permission) {
            return static::creates(['name' => $name, 'guard_name' => $guardName]);
        }
        return $permission;
    }

    protected static function getPermissions(array $params = []): Collection
    {
        return app(PermissionRegistrar::class)
            ->setPermissionClass(static::class)
            ->getPermissions($params);
    }
}