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
use tp5er\think\auth\sanctum\exceptions\MissingScopeException;

class CheckScopes
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure $next
     * @param mixed ...$scopes
     *
     * @return Response
     *
     * @throws AuthenticationException
     */
    public function handle($request, \Closure $next, ...$scopes)
    {
        try {
            return (new CheckAbilities())->handle($request, $next, ...$scopes);
        } catch (MissingAbilityException $e) {
            throw new MissingScopeException($e->abilities());
        }
    }
}
