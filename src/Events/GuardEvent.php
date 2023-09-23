<?php

namespace tp5er\think\auth\Events;

use think\Event;
use tp5er\think\auth\Contracts\Authenticatable;

/**
 * Trait GuardEvent
 * @package tp5er\think\auth\Events
 */
trait GuardEvent
{
    /**
     * @var Event
     */
    protected $events;

    /**
     * Fire the authenticated event if the dispatcher is set.
     *
     * @param Authenticatable $user
     * @return void
     */
    protected function fireAuthenticatedEvent($user)
    {
        if (isset($this->events)) {
            $this->events->trigger(new Authenticated(
                $this->name, $user
            ));
        }
    }

    /**
     * Fire the login event if the dispatcher is set.
     *
     * @param Authenticatable $user
     * @param bool $remember
     * @return void
     */
    protected function fireLoginEvent($user, $remember = false)
    {
        if (isset($this->events)) {
            $this->events->trigger(new Login(
                $this->name, $user, $remember
            ));
        }
    }

    /**
     * Fire the attempt event with the arguments.
     *
     * @param array $credentials
     * @param bool $remember
     * @return void
     */
    protected function fireAttemptEvent(array $credentials, $remember = false)
    {
        if (isset($this->events)) {
            $this->events->trigger(new Attempting(
                $this->name, $credentials, $remember
            ));
        }
    }

    /**
     * Fires the validated event if the dispatcher is set.
     *
     * @param $user
     */
    protected function fireValidatedEvent($user)
    {
        if (isset($this->events)) {
            $this->events->trigger(new Validated(
                $this->name, $user
            ));

        }
    }

    /**
     * Fire the failed authentication attempt event with the given arguments.
     *
     * @param Authenticatable|null $user
     * @param array $credentials
     * @return void
     */
    protected function fireFailedEvent($user, array $credentials)
    {
        if (isset($this->events)) {
            $this->events->trigger(new Failed(
                $this->name, $user, $credentials
            ));
        }
    }

    /**
     * Logout event
     * @param Authenticatable $user
     * @return void
     */
    protected function currentDeviceLogout($user)
    {
        if (isset($this->events)) {
            $this->events->trigger(new CurrentDeviceLogout($this->name, $user));
        }
    }
}