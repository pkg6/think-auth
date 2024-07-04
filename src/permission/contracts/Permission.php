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
use tp5er\think\auth\permission\exceptions\PermissionDoesNotExist;

interface Permission
{
    /**
     * A permission can be applied to roles.
     */
    public function roles(): BelongsToMany;

    /**
     * Find a permission by its name.
     *
     *
     * @throws PermissionDoesNotExist
     */
    public static function findByName(string $name, $guardName): self;

    /**
     * Find a permission by its id.
     *
     *
     * @throws PermissionDoesNotExist
     */
    public static function findById($id, $guardName): self;

    /**
     * Find or Create a permission by its name and guard name.
     */
    public static function findOrCreate(string $name, $guardName): self;
}
