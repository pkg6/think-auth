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

namespace tp5er\think\auth\permission\models;

use think\Model;
use think\model\relation\BelongsToMany;
use tp5er\think\auth\permission\contracts\Permission as PermissionContract;
use tp5er\think\auth\permission\exceptions\PermissionAlreadyExists;
use tp5er\think\auth\permission\exceptions\PermissionDoesNotExist;
use tp5er\think\auth\permission\Guard;

class Permission extends Model implements PermissionContract
{
    protected $guarded = [];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public static function create(array $data, array $allowField = [], bool $replace = false, string $suffix = ''): Model
    {
        $data['guard_name'] = $data['guard_name'] ?? Guard::getDefaultName(static::class);
        $permission = static::getPermission(['name' => $data['name'], 'guard_name' => $data['guard_name']]);
        if ($permission) {
            throw PermissionAlreadyExists::create($data['name'], $data['guard_name']);
        }
        return parent::create($data, $allowField, $replace, $suffix);
    }

    protected static function getPermission(array $params = [])
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public static function findById($id, $guardName = null): PermissionContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $permission = static::getPermission([(new static())->getKey() => $id, 'guard_name' => $guardName]);
        if (!$permission) {
            throw PermissionDoesNotExist::withId($id, $guardName);
        }
        return $permission;
    }

    /**
     * @inheritDoc
     */
    public static function findByName(string $name, $guardName = null): PermissionContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $permission = static::getPermission(['name' => $name, 'guard_name' => $guardName]);
        if (!$permission) {
            throw PermissionDoesNotExist::create($name, $guardName);
        }
        return $permission;
    }

    /**
     * @inheritDoc
     */
    public static function findOrCreate(string $name, $guardName): PermissionContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $permission = static::getPermission(['name' => $name, 'guard_name' => $guardName]);

        if (!$permission) {
            return static::create(['name' => $name, 'guard_name' => $guardName]);
        }
        return $permission;
    }

}
