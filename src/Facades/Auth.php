<?php


namespace tp5er\think\auth\Facades;

use think\Facade;
use tp5er\think\auth\Contracts\Authenticatable;
use tp5er\think\auth\Contracts\Guard;
use tp5er\think\auth\Contracts\StatefulGuard;

/**
 * Class Auth
 * @method static Guard|StatefulGuard guard(string|null $name = null)
 * @method static bool check()
 * @method static bool guest()
 * @method static Authenticatable|null user()
 * @method static int|null id()
 * @method static bool validate(array $credentials = [])
 * @method static void setUser(Authenticatable $user)
 * @method static bool attempt(array $credentials = [], bool $remember = false)
 * @method static bool once(array $credentials = [])
 * @method static void login(Authenticatable $user, bool $remember = false)
 * @method static Authenticatable loginUsingId(mixed $id, bool $remember = false)
 * @method static bool onceUsingId(mixed $id)
 * @method static bool viaRemember()
 * @method static void logout()
 *
 */
class Auth extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'auth.guard';
    }
}