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

use tp5er\think\auth\access\Register as accessRegister;
use tp5er\think\auth\contracts\AuthManagerInterface;
use tp5er\think\auth\contracts\Factory;
use tp5er\think\auth\contracts\GateInterface;
use tp5er\think\auth\contracts\Guard;
use tp5er\think\auth\contracts\StatefulGuard;
use tp5er\think\auth\jwt\JWTAuth;
use tp5er\think\auth\jwt\Register as JWTRegister;
use tp5er\think\auth\JWTGuard;
use tp5er\think\auth\keyparser\Register as keyparserRegister;

if (!function_exists('auth')) {

    /**
     * @param $guard
     *
     * @return Guard|StatefulGuard|Factory|AuthManagerInterface|JWTGuard|JWTAuth
     */
    function auth($guard = null)
    {
        if (is_null($guard)) {
            return app()->get(Factory::class);
        }

        return app()->get(Factory::class)->guard($guard);
    }
}

if (!function_exists('gate')) {
    /**
     * @return GateInterface
     */
    function gate()
    {
        return app()->get(accessRegister::gate);
    }
}

if (!function_exists('key_parser')) {
    /**
     * 获取token的方式.
     *
     * @return \tp5er\think\auth\contracts\KeyParserFactory
     */
    function key_parser()
    {
        return app()->get(keyparserRegister::keyParser);
    }
}

if (!function_exists('jwt')) {
    /**
     * @return JWTAuth
     */
    function jwt()
    {
        return app()->get(JWTRegister::auth);
    }
}

if (!function_exists('requesta')) {
    /**
     * @return \tp5er\think\auth\Request
     */
    function requesta()
    {
        return app()->get(\tp5er\think\auth\contracts\Request::class);
    }
}
if (!function_exists('requestSetUserResolver')) {
    function setRequestUserResolver(callable $resolver, $requestAlias = [\think\Request::class, \tp5er\think\auth\contracts\Request::class])
    {
        $ret = true;
        foreach ($requestAlias as $request) {
            if (app()->has($request)) {
                $request = app()->get($request);
                if (method_exists($request, 'setUserResolver')) {
                    $request->setUserResolver($resolver);
                } else {
                    $ret = false;
                }
            } else {
                $ret = false;
            }
        }
        return $ret;
    }
}

if (!function_exists('with')) {
    /**
     * Return the given value, optionally passed through the given callback.
     *
     * @param mixed $value
     * @param callable|null $callback
     *
     * @return mixed
     */
    function with($value, callable $callback = null)
    {
        return is_null($callback) ? $value : $callback($value);
    }
}

if (!function_exists('head')) {
    /**
     * Get the first element of an array. Useful for method chaining.
     *
     * @param array $array
     *
     * @return mixed
     */
    function head($array)
    {
        return reset($array);
    }
}



