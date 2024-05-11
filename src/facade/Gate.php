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

namespace tp5er\think\auth\facade;

use tp5er\think\auth\access\Response;
use tp5er\think\auth\contracts\Authenticatable;
use tp5er\think\auth\contracts\GateInterface;

/**
 * Class Gate.
 *
 * @method static GateInterface guessPolicyNamesUsing(callable $callback)
 * @method static Response authorize(string $ability, array|mixed $arguments = [])
 * @method static Response inspect(string $ability, array|mixed $arguments = [])
 * @method static Response allowIf(\Closure|bool $condition, string|null $message = null, mixed $code = null)
 * @method static Response denyIf(\Closure|bool $condition, string|null $message = null, mixed $code = null)
 * @method static GateInterface after(callable $callback)
 * @method static GateInterface before(callable $callback)
 * @method static GateInterface define(string $ability, callable|string $callback)
 * @method static GateInterface forUser(Authenticatable|mixed $user)
 * @method static GateInterface policy(string $class, string $policy)
 * @method static array abilities()
 * @method static bool allows(string $ability, array|mixed $arguments = [])
 * @method static bool any(iterable|string $abilities, array|mixed $arguments = [])
 * @method static bool check(iterable|string $abilities, array|mixed $arguments = [])
 * @method static bool denies(string $ability, array|mixed $arguments = [])
 * @method static bool has(string $ability)
 * @method static mixed getPolicyFor(object|string $class)
 * @method static mixed raw(string $ability, array|mixed $arguments = [])
 *
 * @see GateInterface
 */
class Gate extends \think\Facade
{
    /**
     * @return string
     */
    protected static function getFacadeClass()
    {
        return GateInterface::class;
    }
}
