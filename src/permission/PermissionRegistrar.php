<?php

namespace tp5er\think\auth\permission;

use think\App;
use think\model\Collection;
use tp5er\think\auth\permission\models\Permission;
use tp5er\think\auth\permission\models\Role;

class PermissionRegistrar
{
    /**
     * @var App
     */
    protected $app;
    /**
     * @var string
     */
    public static $cacheKey = 'auth.permission.cache';

    /** @var \DateInterval|int */
    public static $cacheExpirationTime;
    /**
     * @var string
     */
    public $pivotRole = 'role_id';
    /**
     * @var string
     */
    public $pivotPermission = 'permission_id';
    /**
     * @var string
     */
    public $roleHasPermissionTable = 'role_has_permissions';
    /**
     * @var string
     */
    public $modelClassPermission = Permission::class;
    /**
     * @var string
     */
    public $modelClassRole = Role::class;

    /**
     * @var \think\model\Collection
     */
    protected $permissions;

    /**
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->initializeCache();
    }

    /**
     * @return void
     */
    public function initializeCache()
    {
        self::$cacheKey = config('auth.permission.cache.key', 'auth.permission.cache');
        self::$cacheExpirationTime = config('auth.permission.cache.expiration_time', \DateInterval::createFromDateString('24 hours'));

        $this->modelClassRole = config('auth.permission.models.role', Role::class);
        $this->modelClassPermission = config('auth.permission.models.permission', Permission::class);
        $this->roleHasPermissionTable = config('auth.permission.table_names.role_has_permission', 'role_has_permissions');
        $this->pivotRole = config('auth.permission.column_names.role_pivot_key', 'role_id');
        $this->pivotPermission = config('auth.permission.column_names.permission_pivot_key', 'permission_id');
    }

    public function setPermissionClass($permissionClass)
    {
        $this->modelClassPermission = $permissionClass;
        return $this;
    }

    public function getPermissionClass()
    {
        return $this->modelClassPermission;
    }

    public function getPermissions(array $params = []): Collection
    {
        if ($this->permissions === null) {
            $this->permissions = $this->app
                ->cache
                ->remember(self::$cacheKey, function () {
                    return $this->getPermissionClass()::newQuery()
                        ->with('roles')
                        ->select();
                }, self::$cacheExpirationTime);
        }
        $permissions = clone $this->permissions;
        foreach ($params as $attr => $value) {
            $permissions = $permissions->where($attr, $value);
        }
        return $permissions;
    }

    public static function isUid($value): bool
    {
        if (!is_string($value) || empty(trim($value))) {
            return false;
        }
        // check if is UUID/GUID
        $uid = preg_match('/^[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}$/iD', $value) > 0;
        if ($uid) {
            return true;
        }
        // check if is ULID
        $ulid = strlen($value) == 26 && strspn($value, '0123456789ABCDEFGHJKMNPQRSTVWXYZabcdefghjkmnpqrstvwxyz') == 26 && $value[0] <= '7';
        if ($ulid) {
            return true;
        }
        return false;
    }
}