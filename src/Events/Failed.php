<?php

namespace tp5er\think\auth\Events;

use tp5er\think\auth\Contracts\Authenticatable;

/**
 * Class Failed
 * @package tp5er\think\auth\Events
 */
class Failed
{
    /**
     * The authentication guard name.
     *
     * @var string
     */
    public $guard;

    /**
     * The user the attempter was trying to authenticate as.
     *
     * @var Authenticatable|null
     */
    public $user;

    /**
     * The credentials provided by the attempter.
     *
     * @var array
     */
    public $credentials;

    /**
     * Create a new event instance.
     *
     * @param string $guard
     * @param Authenticatable|null $user
     * @param array $credentials
     * @return void
     */
    public function __construct($guard, $user, $credentials)
    {
        $this->user        = $user;
        $this->guard       = $guard;
        $this->credentials = $credentials;
    }
}