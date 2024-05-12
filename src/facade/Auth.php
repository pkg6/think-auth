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

use tp5er\think\auth\contracts\Authenticatable;
use tp5er\think\auth\contracts\AuthManagerInterface;
use tp5er\think\auth\contracts\Factory;
use tp5er\think\auth\contracts\Guard;
use tp5er\think\auth\contracts\StatefulGuard;

/**
 * Class Auth.
 *
 * @method static AuthManagerInterface extend($driver, \Closure $callback)
 * @method static AuthManagerInterface provider($name, \Closure $callback)
 * @method static AuthManagerInterface setConfigGuardProvider($guardName, $tableOrModel = "", $guardDriver = "session")
 * @method static Guard|StatefulGuard  guard($name = null)
 * @method static Authenticatable user()
 * @method static bool once(array $credentials = [])
 * @method static Authenticatable|bool onceUsingId($id)
 * @method static void login(Authenticatable $user, $remember = false)
 * @method static Authenticatable|bool loginUsingId($id, $remember = false)
 * @method static bool attempt(array $credentials = [], $remember = false)
 * @method static void logout()
 */
class Auth extends \think\Facade
{
    /**
     * @return string
     */
    protected static function getFacadeClass()
    {
        return Factory::class;
    }
}
