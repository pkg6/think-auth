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

namespace tp5er\think\auth\permission\exceptions;

use InvalidArgumentException;

class PermissionDoesNotExist extends InvalidArgumentException
{
    public static function create(string $permissionName, ?string $guardName)
    {
        return new static("There is no permission named `{$permissionName}` for guard `{$guardName}`.");
    }

    /**
     * @param  int|string  $permissionId
     *
     * @return static
     */
    public static function withId($permissionId, ?string $guardName)
    {
        return new static("There is no [permission] with ID `{$permissionId}` for guard `{$guardName}`.");
    }
}
