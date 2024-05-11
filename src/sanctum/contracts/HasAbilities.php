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

namespace tp5er\think\auth\sanctum\contracts;

interface HasAbilities
{
    /**
     * Determine if the token has a given ability.
     *
     * @param  string  $ability
     *
     * @return bool
     */
    public function can($ability);

    /**
     * Determine if the token is missing a given ability.
     *
     * @param  string  $ability
     *
     * @return bool
     */
    public function cant($ability);
}
