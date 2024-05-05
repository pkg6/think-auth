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

use think\helper\Str;
use tp5er\think\auth\contracts\AuthManagerInterface;
use tp5er\think\auth\contracts\Factory;
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
            return app()->get("auth");
        }

        return app()->get("auth")->guard($guard);
    }
}

if ( ! function_exists('str_parse_callback')) {

    /**
     * Parse a Class[@]method style callback into class and method.
     *
     * @param  string  $callback
     * @param  string|null  $default
     *
     * @return array<int, string|null>
     */
    function str_parse_callback($callback, $default = null)
    {

        return Str::contains($callback, '@') ? explode('@', $callback, 2) : [$callback, $default];
    }
}
