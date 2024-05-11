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

namespace tp5er\think\auth;

use think\Request;
use tp5er\think\auth\contracts\Authenticatable;
use tp5er\think\auth\contracts\Guard;
use tp5er\think\auth\contracts\UserProvider;

class RequestGuard implements Guard
{
    use GuardHelpers;

    /**
     * The guard callback.
     *
     * @var callable
     */
    protected $callback;

    /**
     * The request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * Create a new authentication guard.
     *
     * @param  callable  $callback
     * @param  Request  $request
     * @param  UserProvider|null  $provider
     *
     * @return void
     */
    public function __construct(callable $callback, Request $request, UserProvider $provider = null)
    {
        $this->request = $request;
        $this->callback = $callback;
        $this->provider = $provider;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return Authenticatable
     */
    public function user()
    {
        // If we've already retrieved the user for the current request we can just
        // return it back immediately. We do not want to fetch the user data on
        // every call to this method because that would be tremendously slow.
        if ( ! is_null($this->user)) {
            return $this->user;
        }

        return $this->user = call_user_func(
            $this->callback,
            $this->request,
            $this->getProvider()
        );
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     *
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return ! is_null((new static(
            $this->callback, $credentials['request'], $this->getProvider()
        ))->user());
    }

    /**
     * Set the current request instance.
     *
     * @param  Request  $request
     *
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }
}
