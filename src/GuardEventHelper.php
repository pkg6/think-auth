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

use tp5er\think\auth\contracts\Authenticatable;
use tp5er\think\auth\events\Attempting;
use tp5er\think\auth\events\Authenticated;
use tp5er\think\auth\events\Failed;
use tp5er\think\auth\events\Login;
use tp5er\think\auth\events\OtherDeviceLogout;

trait GuardEventHelper
{
    /**
     * The name of the guard. Typically "web".
     *
     * Corresponds to guard name in authentication configuration.
     *
     * @var string
     */
    protected $name;

    /**
     * @param Authenticatable $user
     *
     * @return void
     */
    protected function fireAuthenticatedEvent($user)
    {
        $this->app->event->trigger(new Authenticated($this->name, $user));
    }

    /**
     * @param array $credentials
     * @param $remember
     *
     * @return void
     */
    protected function fireAttemptEvent($credentials, $remember)
    {
        $this->app->event->trigger(new Attempting($this->name, $credentials, $remember));
    }

    /**
     * @param Authenticatable $user
     * @param array $credentials
     *
     * @return void
     */
    protected function fireFailedEvent($user, $credentials)
    {
        $this->app->event->trigger(new Failed($this->name, $user, $credentials));
    }

    /**
     * @param Authenticatable $user
     * @param bool $remember
     *
     * @return void
     */
    protected function fireLoginEvent($user, $remember = false)
    {
        $this->app->event->trigger(new Login($this->name, $user, $remember));
    }
    /**
     * Fire the other device logout event if the dispatcher is set.
     *
     * @param  Authenticatable  $user
     *
     * @return void
     */
    protected function fireOtherDeviceLogoutEvent($user)
    {
        $this->app->event->trigger(new OtherDeviceLogout($this->name, $user));
    }

}
