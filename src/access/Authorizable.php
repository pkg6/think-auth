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

namespace tp5er\think\auth\access;

trait Authorizable
{
    /**
     * Determine if the entity has the given abilities.
     *
     * @param  iterable|string  $abilities
     * @param  array|mixed  $arguments
     *
     * @return bool
     */
    public function can($abilities, $arguments = [])
    {
        return gate()->forUser($this)->check($abilities, $arguments);
    }

    /**
     * Determine if the entity has any of the given abilities.
     *
     * @param  iterable|string  $abilities
     * @param  array|mixed  $arguments
     *
     * @return bool
     */
    public function canAny($abilities, $arguments = [])
    {
        return gate()->forUser($this)->any($abilities, $arguments);
    }

    /**
     * Determine if the entity does not have the given abilities.
     *
     * @param  iterable|string  $abilities
     * @param  array|mixed  $arguments
     *
     * @return bool
     */
    public function cant($abilities, $arguments = [])
    {
        return ! $this->can($abilities, $arguments);
    }

    /**
     * Determine if the entity does not have the given abilities.
     *
     * @param  iterable|string  $abilities
     * @param  array|mixed  $arguments
     *
     * @return bool
     */
    public function cannot($abilities, $arguments = [])
    {
        return $this->cant($abilities, $arguments);
    }
}
