<?php

namespace tp5er\think\auth\Facades;

use think\Facade;
use tp5er\think\auth\Contracts\Authenticatable;
use tp5er\think\auth\Guards\Guard;
use tp5er\think\auth\JwtAuthManager;
use tp5er\think\auth\Support\Payload;

/**
 * Class JwtAuth
 * @package tp5er\think\auth\Facades
 * @method static JwtAuthManager setAuth($name = null)
 * @method static Guard auth()
 * @method static JwtAuthManager setToken($token)
 * @method static Payload getPayload()
 * @method static string attempt(array $credentials = [])
 * @method static Authenticatable|null authenticate()
 * @method static string refresh()
 * @method static int id()
 * @method static string getRequestToken()
 *
 */
class JwtAuth extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'auth.jwt';
    }
}