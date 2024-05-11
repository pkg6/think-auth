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

namespace tp5er\think\auth\contracts;

use Closure;

interface AuthManagerInterface
{
    /**
     * Get the user resolver callback.
     *
     * @return \Closure
     */
    public function userResolver();

    /**
     * Set the callback to be used to resolve users.
     *
     * @param \Closure $userResolver
     *
     * @return $this
     */
    public function resolveUsersUsing(Closure $userResolver);

    /**
     * Get the default authentication driver name.
     *
     * @return string
     */
    public function getDefaultDriver();

    /**
     * Set the default authentication driver name.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setDefaultDriver($name);

    /**
     * Register a new callback based request guard.
     *
     * @param  string  $driver
     * @param  callable  $callback
     *
     * @return $this
     */
    public function viaRequest($driver, callable $callback);

    /**
     * 设置动态驱动配置.
     *
     * @param string $guard
     * @param string $tableOrModel
     * @param string $guardDriver
     *
     * @return $this
     */
    public function setConfigGuardProvider($guard, $tableOrModel, $guardDriver = "session");

    /**
     * Register a custom driver creator Closure.
     *
     * @param string $driver
     * @param Closure $callback
     *
     * @return $this
     */
    public function extend($driver, Closure $callback);

    /**
     * Register a custom provider creator Closure.
     *
     * @param string $name
     * @param Closure $callback
     *
     * @return $this
     */
    public function provider($name, Closure $callback);

    /**
     * Determines if any guards have already been resolved.
     *
     * @return bool
     */
    public function hasResolvedGuards();

    /**
     * Forget all of the resolved guard instances.
     *
     * @return $this
     */
    public function forgetGuards();
}
