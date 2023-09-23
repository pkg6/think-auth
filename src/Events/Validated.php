<?php

namespace tp5er\think\auth\Events;

use tp5er\think\auth\Contracts\Authenticatable;

/**
 * Class Validated
 * @package tp5er\think\auth\Events
 */
class Validated
{
    /**
     * The authentication guard name.
     *
     * @var string
     */
    public $guard;

    /**
     * The user retrieved and validated from the User Provider.
     *
     * @var Authenticatable
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param string $guard
     * @param Authenticatable $user
     * @return void
     */
    public function __construct($guard, $user)
    {
        $this->user  = $user;
        $this->guard = $guard;
    }
}