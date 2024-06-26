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

class CheckAbilities
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure $next
     * @param mixed ...$abilities
     *
     * @return Response
     *
     * @throws AuthenticationException
     * @throws MissingAbilityException
     */
    public function handle($request, \Closure $next, ...$abilities)
    {
        if ( ! requesta()->user() || ! requesta()->user()->currentAccessToken()) {
            throw new AuthenticationException;
        }
        foreach ($abilities as $ability) {
            if ( ! requesta()->user()->tokenCan($ability)) {
                throw new MissingAbilityException($ability);
            }
        }

        return $next($request);
    }
}
