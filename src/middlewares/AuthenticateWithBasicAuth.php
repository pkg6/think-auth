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

namespace tp5er\think\auth\middlewares;

use think\Response;

class AuthenticateWithBasicAuth
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure $next
     * @param null $guard
     * @param null $field
     *
     * @return Response
     */
    public function handle($request, \Closure $next, $guard = null, $field = null)
    {
        auth()->guard($guard)->basic($field ?: 'email');

        return $next($request);
    }
}
