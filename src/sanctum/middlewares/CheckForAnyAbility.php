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

namespace tp5er\think\auth\sanctum\middlewares;

use think\Response;
use tp5er\think\auth\exceptions\AuthenticationException;
use tp5er\think\auth\sanctum\exceptions\MissingAbilityException;

class CheckForAnyAbility
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure $next
     * @param mixed ...$guards
     *
     * @return Response
     *
     * @throws AuthenticationException
     */
    public function handle($request, \Closure $next, ...$abilities)
    {
        if ( ! requestUser() || ! requestUser()->currentAccessToken()) {
            throw new AuthenticationException;
        }
        foreach ($abilities as $ability) {
            if (requestUser()->tokenCan($ability)) {
                throw new MissingAbilityException($ability);
            }
        }

        return $next($request);
    }
}
