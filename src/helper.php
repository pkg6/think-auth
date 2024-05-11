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
use tp5er\think\auth\contracts\Factory;
use tp5er\think\auth\contracts\GateInterface;
use tp5er\think\auth\contracts\Guard;
use tp5er\think\auth\contracts\StatefulGuard;

if ( ! function_exists('auth')) {

    /**
     * @param $guard
     *
     * @return Guard|StatefulGuard|Factory|AuthManagerInterface
     */
    function auth($guard = null)
    {
        if (is_null($guard)) {
            return app()->get(Factory::class);
        }

        return app()->get(Factory::class)->guard($guard);
    }
}

if ( ! function_exists('request_user')) {

    /**
     * Get the user making the request.
     *
     * @param string|null $guard
     *
     * @return mixed
     */
    function request_user($guard = null)
    {
        return call_user_func(app()->get(Authenticatable::class), $guard);
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

if ( ! function_exists('policy')) {
    /**
     * Get a policy instance for a given class.
     *
     * @param  object|string  $class
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    function policy($class)
    {
        return app(GateInterface::class)->getPolicyFor($class);
    }
}
