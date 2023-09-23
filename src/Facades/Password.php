<?php

namespace tp5er\think\auth\Facades;

use think\Facade;
use tp5er\think\auth\Contracts\Authenticatable;

/**
 * Class Password
 * @package tp5er\think\auth\Facades
 * @method static string encrypt(string $password)
 * @method static bool verify(Authenticatable $user, string $password)
 */
class Password extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'auth.password';
    }
}