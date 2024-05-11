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

use think\Request;
use think\Response;
use tp5er\think\auth\exceptions\AuthenticationException;
use tp5er\think\auth\sanctum\exceptions\MissingAbilityException;
use tp5er\think\auth\sanctum\exceptions\MissingScopeException;

class CheckForAnyScope
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @param \Closure $next
     * @param mixed ...$scopes
     *
     * @return Response
     *
     * @throws AuthenticationException
     * @throws MissingScopeException
     */
    public function handle($request, $next, ...$scopes)
    {
        try {
            return (new CheckForAnyAbility())->handle($request, $next, ...$scopes);
        } catch (MissingAbilityException $e) {
            throw new MissingScopeException($e->abilities());
        }
    }
}
