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

class PermissionRegistrar
{

    /** @var string */
    public static $pivotRole;

    /** @var string */
    public static $pivotPermission;

    /** @var \DateInterval|int */
    public static $cacheExpirationTime;
    /** @var bool */
    public static $teams;
    /** @var string */
    public static $teamsKey;
    /** @var string */
    public static $cacheKey;
    /**
     * @var App
     */
    protected $app;
    /**
     * @var \think\Cache
     */
    protected $cache;
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->initializeCache();
    }

    public function initializeCache()
    {
        $this->permissionClass = Register::getConfig('models.permission');
        $this->roleClass = Register::getConfig('models.role');
        self::$cacheExpirationTime = Register::getConfig('cache.expiration_time', 24 * 60 * 60);
        self::$teams = Register::getConfig('teams', false);
        self::$teamsKey = Register::getConfig('column_names.team_foreign_key');
        self::$cacheKey = Register::getConfig('cache.key');
        self::$pivotRole = Register::getConfig('column_names.role_pivot_key', 'role_id');
        self::$pivotPermission = Register::getConfig('column_names.permission_pivot_key', 'permission_id');
        $this->cache = $this->app->cache;
    }
}
