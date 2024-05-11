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
