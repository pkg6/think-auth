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

namespace tp5er\think\auth\permission;

use think\App;
use think\helper\Arr;
use tp5er\think\auth\permission\contracts\Permission as PermissionContract;
use tp5er\think\auth\permission\contracts\Role as RoleContract;

class Register
{
    /**
     *
     */
    const config = "permission";

    /**
     * @var array
     */
    public static $config = [
        'models' => [
            'permission' => \tp5er\think\auth\permission\models\Permission::class,
            'role' => \tp5er\think\auth\permission\models\Role::class,
        ],
        'table_names' => [
            'roles' => 'roles',
            'permissions' => 'permissions',
            'model_has_permissions' => 'model_has_permissions',
            'model_has_roles' => 'model_has_roles',
            'role_has_permissions' => 'role_has_permissions',
        ],
        'column_names' => [
            'role_pivot_key' => null,
            'permission_pivot_key' => null,
            'model_morph_key' => 'model_id',
            'team_foreign_key' => 'team_id',
        ],
        'register_permission_check_method' => true,
        'teams' => false,
        'display_permission_in_exception' => false,
        'display_role_in_exception' => false,
        'enable_wildcard_permission' => false,
        'cache' => [
            'expiration_time' => 24 * 60 * 60,
            'key' => 'tp5er.auth.permission.cache',
            'store' => 'default',
            'column_names_except'=>['create_time','update_time','delete_time'],
        ]
    ];

    /**
     * @param array $config
     * @return void
     */
    public static function mergeConfig(array $config = [])
    {
        self::$config = array_merge(self::$config, $config);
    }

    /**
     * @param $key
     * @param $value
     * @return void
     */
    public static function setConfig($key, $value)
    {
        Arr::set(self::$config, $key, $value);
        if (is_subclass_of($value, PermissionContract::class)) {
            \app()->bind(PermissionContract::class, $value);
        }
        if (is_subclass_of($value, RoleContract::class)) {
            \app()->bind(RoleContract::class, $value);
        }
        \app()->config->set(['permission' => self::$config], 'auth');
    }

    /**
     * @param $key
     * @param $default
     * @return array|\ArrayAccess|mixed
     */
    public static function getConfig($key = null, $default = null)
    {
        if (is_null($key)) {
            return self::$config;
        }
        return Arr::get(self::$config, $key, $default);
    }

    /**
     * @param App $app
     * @param array $config
     * @return void
     */
    public static function bind(App $app, array $config = [])
    {
        self::mergeConfig($config);

        $app->bind(PermissionContract::class, self::getConfig('models.permission'));
        $app->bind(RoleContract::class, self::getConfig('models.role'));

        $app->bind(PermissionRegistrar::class, function () use (&$app) {
            $permissionLoader = new PermissionRegistrar($app, self::getConfig());
            if (self::getConfig('register_permission_check_method')) {
                $permissionLoader->clearClassPermissions();
                $permissionLoader->registerPermissions();
            }
            return $permissionLoader;
        });
    }
}
