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

namespace tp5er\think\auth\facade;

use Casbin\Enforcer;
use Casbin\Model\Model;

/**
 * @see Enforcer
 * @mixin Enforcer
 *
 * @method mixed GetRolesForUser(string $username) static 获取用户具有的角色
 * @method mixed GetUsersForRole(string $role) static 获取具有角色的用户
 * @method mixed HasRoleForUser(string $username, string $role) static 确定用户是否具有角色
 * @method mixed AddRoleForUser(string $username, string $role) static 为用户添加角色
 * @method mixed DeleteRoleForUser(string $username, string $role) static 删除用户的角色
 * @method mixed DeleteRolesForUser(string $username) static 删除用户的所有角色
 * @method mixed DeleteUser(string $username) static 删除一个用户
 * @method mixed DeleteRole(string $role) static 删除一个角色
 * @method mixed DeletePermission(string $policy) static 删除权限
 * @method mixed AddPermissionForUser(string $username, string $policy) static 为用户或角色添加权限
 * @method mixed DeletePermissionForUser(string $username, string $policy) static 删除用户或角色的权限
 * @method mixed DeletePermissionsForUser(string $username) static 删除用户或角色的权限
 * @method mixed GetPermissionsForUser(string $username) static 获取用户或角色的权限
 * @method mixed HasPermissionForUser(string $username, string $policy) static 确定用户是否具有权限
 * @method mixed GetImplicitRolesForUser(string $username) static 获取用户具有的隐式角色
 * @method mixed GetImplicitPermissionsForUser(string $username) static 获取用户具有的隐式角色
 */
class Casbin extends \think\Facade
{
    /**
     * @return string
     */
    protected static function getFacadeClass()
    {
        return Model::class;
    }
}
