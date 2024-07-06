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

use think\db\Query;
use think\Model;
use think\model\relation\BelongsToMany;
use tp5er\think\auth\permission\contracts\Role as RoleContract;
use tp5er\think\auth\permission\exceptions\RoleAlreadyExists;
use tp5er\think\auth\permission\exceptions\RoleDoesNotExist;
use tp5er\think\auth\permission\Guard;
use  tp5er\think\auth\permission\PermissionRegistrar;

class Role extends Model implements RoleContract
{
    protected $guarded = [];

    public static function create(array $data, array $allowField = [], bool $replace = false, string $suffix = ''): Model
    {
        $data['guard_name'] = $data['guard_name'] ?? Guard::getDefaultName(static::class);
        $params = ['name' => $data['name'], 'guard_name' => $data['guard_name']];
        if (PermissionRegistrar::$teams) {
            if (array_key_exists(PermissionRegistrar::$teamsKey, $data)) {
                $params[PermissionRegistrar::$teamsKey] = $data[PermissionRegistrar::$teamsKey];
            } else {
                $data[PermissionRegistrar::$teamsKey] = getPermissionsTeamId();
            }
        }
        if (static::findByParam($params)) {
            throw RoleAlreadyExists::create($data['name'], $data['guard_name']);
        }

        return parent::create($data, $allowField, $replace, $suffix);
    }

    public static function findByName($name, $guardName = null): RoleContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $role = static::findByParam(['name' => $name, 'guard_name' => $guardName]);
        if ( ! $role) {
            throw RoleDoesNotExist::named($name);
        }

        return $role;
    }

    public static function findById($id, $guardName = null): RoleContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $role = static::findByParam([(new static())->getPk() => $id, 'guard_name' => $guardName]);
        if ( ! $role) {
            throw RoleDoesNotExist::withId($id);
        }

        return $role;
    }

    public static function findOrCreate($name, $guardName = null): RoleContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $role = static::findByParam(['name' => $name, 'guard_name' => $guardName]);
        if ( ! $role) {
            return self::create(['name' => $name, 'guard_name' => $guardName] + (PermissionRegistrar::$teams ? [PermissionRegistrar::$teamsKey => getPermissionsTeamId()] : []));
        }

        return $role;
    }

    protected static function findByParam(array $params)
    {
        $query = static::newQuery();
        if (PermissionRegistrar::$teams) {
            $query->where(function (Query $q) use ($params) {
                $q->whereNull(PermissionRegistrar::$teamsKey)
                    ->whereOr(PermissionRegistrar::$teamsKey, $params[PermissionRegistrar::$teamsKey] ?? getPermissionsTeamId());
            });
            unset($params[PermissionRegistrar::$teamsKey]);
        }
        foreach ($params as $key => $value) {
            $query->where($key, $value);
        }

        return $query->find();
    }

    public function permissions(): BelongsToMany
    {
        return  $this->belongsToMany(Permission::class);
    }

    public function hasPermissionTo($permission, $guardName): bool
    {
        // TODO: Implement hasPermissionTo() method.
    }
}
