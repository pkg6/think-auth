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

use tp5er\think\auth\contracts\Authenticatable;
use tp5er\think\auth\contracts\AuthManagerInterface;
use tp5er\think\auth\contracts\Authorizable;
use tp5er\think\auth\contracts\Factory;
use tp5er\think\auth\contracts\GateInterface;
use tp5er\think\auth\contracts\Guard;
use tp5er\think\auth\contracts\StatefulGuard;
use tp5er\think\auth\jwt\JWTAuth;
use tp5er\think\auth\JWTGuard;

if ( ! function_exists('auth')) {

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

if ( ! function_exists('gate')) {
    /**
     * @return GateInterface
     */
    function gate()
    {
        return app()->get(GateInterface::class);
    }
}

if ( ! function_exists('key_parser')) {
    /**
     * 获取token的方式
     * @return \tp5er\think\auth\contracts\KeyParserFactory
     */
    function key_parser()
    {
        return app()->get(\tp5er\think\auth\contracts\KeyParserFactory::class);
    }
}

if ( ! function_exists('jwt')) {
    /**
     * @return JWTAuth
     */
    function jwt()
    {
        return app()->get(JWTAuth::class);
    }
}

if ( ! function_exists('requestUser')) {

    /**
     * Get the user making the request.
     *
     * @param string|null $guard
     *
     * @return Authenticatable|Authorizable
     */
    function requestUser($guard = null)
    {
        return call_user_func(app()->get(Authenticatable::class), $guard);
    }
}

if ( ! function_exists('requestBearerToken')) {

    /**
     * @return false|string|null
     */
    function requestBearerToken()
    {
        $header = app()->request->header("Authorization", "");
        $position = strrpos($header, 'Bearer ');
        if ($position !== false) {
            $header = substr($header, $position + 7);

            return strpos($header, ',') !== false ? strstr($header, ',', true) : $header;
        }

        return null;
    }
}

if ( ! function_exists('requestGetUser')) {

    /**
     * @return array|string|null
     */
    function requestGetUser()
    {
        return app()->request->header("PHP_AUTH_USER");
    }
}

if ( ! function_exists('requestGetPassword')) {

    /**
     * @return array|string|null
     */
    function requestGetPassword()
    {
        return app()->request->header("PHP_AUTH_PW");
    }
}

if ( ! function_exists('with')) {
    /**
     * Return the given value, optionally passed through the given callback.
     *
     * @param  mixed  $value
     * @param  callable|null  $callback
     *
     * @return mixed
     */
    function with($value, callable $callback = null)
    {
        return is_null($callback) ? $value : $callback($value);
    }
}
