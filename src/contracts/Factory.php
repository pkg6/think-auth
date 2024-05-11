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

/**
 * @see AuthManager
 */
interface Factory
{
    /**
     * Get a guard instance by name.
     *
     * @param  string|null  $name
     *
     * @return Guard|StatefulGuard
     */
    public function guard($name = null);

    /**
     * Set the default guard the factory should serve.
     *i.
     *
     * @param  string  $name
     *
     * @return void
     */
    public function shouldUse($name);
}
