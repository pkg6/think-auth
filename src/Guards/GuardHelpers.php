<?php

namespace tp5er\think\auth\Guards;

use tp5er\think\auth\Contracts\Authenticatable;
use tp5er\think\auth\Exceptions\AuthenticationException;

/**
 * Trait GuardHelpers
 * @package tp5er\think\auth\Guards
 */
trait GuardHelpers
{
    /**
     * @var Authenticatable
     */
    protected $user;

    /**
     * Determine if current user is authenticated. If not, throw an exception.
     * @return Authenticatable
     * @throws AuthenticationException
     */
    public function authenticate()
    {
        if (!is_null($user = $this->user())) {
            return $user;
        }
        throw new AuthenticationException;
    }

    /**
     * Determine if the guard has a user instance.
     *
     * @return bool
     */
    public function hasUser()
    {
        return !is_null($this->user);
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check()
    {
        return !is_null($this->user());
    }

    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest()
    {
        return !$this->check();
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|null
     */
    public function id()
    {
        if ($this->user()) {
            return $this->user()->getAuthIdentifier();
        }
    }

    /**
     * Set the current user.
     *
     * @param Authenticatable $user
     * @return $this
     */
    public function setUser(Authenticatable $user)
    {
        $this->user = $user;

        return $this;
    }
}