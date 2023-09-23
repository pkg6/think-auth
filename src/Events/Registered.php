<?php

namespace tp5er\think\auth\Events;

use tp5er\think\auth\Contracts\Authenticatable;

/**
 * Class Registered
 * @package tp5er\think\auth\Events
 */
class Registered
{
    /**
     * The authenticated user.
     *
     * @var Authenticatable
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param Authenticatable  $user
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }
}