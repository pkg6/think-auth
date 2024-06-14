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

class RoleDoesNotExist extends InvalidArgumentException
{
    public static function named(string $roleName, ?string $guardName)
    {
        return new static("There is no role named `{$roleName}` for guard `{$guardName}`.");
    }

    /**
     * @param int|string $roleId
     *
     * @return static
     */
    public static function withId($roleId, ?string $guardName)
    {
        return new static("There is no role with ID `{$roleId}` for guard `{$guardName}`.");
    }
}
