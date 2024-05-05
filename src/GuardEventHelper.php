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

use think\App;
use tp5er\think\auth\contracts\Authenticatable;
use tp5er\think\auth\events\Attempting;
use tp5er\think\auth\events\Authenticated;
use tp5er\think\auth\events\Failed;
use tp5er\think\auth\events\Login;
use tp5er\think\auth\events\OtherDeviceLogout;

trait GuardEventHelper
{
    /**
     * @return string
     */
    abstract public function getName();
    /**
     * @return App
     */
    abstract public function getApp();
    /**
     * @param Authenticatable $user
     *
     * @return void
     */
    protected function fireAuthenticatedEvent($user)
    {
        $this->getApp()->event->trigger(new Authenticated($this->getName(), $user));
    }

    /**
     * @param array $credentials
     * @param $remember
     *
     * @return void
     */
    protected function fireAttemptEvent($credentials, $remember)
    {
        $this->getApp()->event->trigger(new Attempting($this->getName(), $credentials, $remember));
    }

    /**
     * @param Authenticatable $user
     * @param array $credentials
     *
     * @return void
     */
    protected function fireFailedEvent($user, $credentials)
    {
        $this->getApp()->event->trigger(new Failed($this->getName(), $user, $credentials));
    }

    /**
     * @param Authenticatable $user
     * @param bool $remember
     *
     * @return void
     */
    protected function fireLoginEvent($user, $remember = false)
    {
        $this->getApp()->event->trigger(new Login($this->getName(), $user, $remember));
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
        $this->getApp()->event->trigger(new OtherDeviceLogout($this->getName(), $user));
    }

}
