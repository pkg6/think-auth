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

namespace tp5er\think\auth\permission\contracts;

use think\model\relation\BelongsToMany;
use tp5er\think\auth\permission\exceptions\RoleDoesNotExist;

interface Role
{
    /**
     * A role may be given various permissions.
     */
    public function permissions(): BelongsToMany;

    /**
     * Find a role by its name and guard name.
     *
     *
     * @throws
     */
    public static function findByName($name, $guardName): self;

    /**
     * Find a role by its id and guard name.
     *
     *
     * @throws RoleDoesNotExist
     */
    public static function findById($id, $guardName): self;

    /**
     * Find or create a role by its name and guard name.
     */
    public static function findOrCreate($name, $guardName): self;

    /**
     * Determine if the user may perform the given permission.
     *
     * @param string|Permission $permission
     */
    public function hasPermissionTo($permission, $guardName): bool;
}
