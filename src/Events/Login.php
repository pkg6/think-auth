<?php

namespace tp5er\think\auth\Events;

use tp5er\think\auth\Contracts\Authenticatable;

/**
 * Class Login
 * @package tp5er\think\auth\Events
 */
class Login
{
    /**
     * The authentication guard name.
     *
     * @var string
     */
    public $guard;

    /**
     * The authenticated user.
     *
     * @var Authenticatable
     */
    public $user;

    /**
     * Indicates if the user should be "remembered".
     *
     * @var bool
     */
    public $remember;

    /**
     * Create a new event instance.
     *
     * @param  string  $guard
     * @param  Authenticatable  $user
     * @param  bool  $remember
     * @return void
     */
    public function __construct($guard, $user, $remember)
    {
        $this->user = $user;
        $this->guard = $guard;
        $this->remember = $remember;
    }
}