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
use tp5er\think\auth\contracts\UserProvider;

abstract class GuardAbstract
{
    use GuardHelpers, GuardEventHelper;

    /**
     * @var App
     */
    protected $app;

    /**
     * @param App $app
     * @param $name
     * @param UserProvider $provider
     */
    public function __construct(App $app, $name, UserProvider $provider)
    {
        $this->app = $app;
        $this->name = $name;
        $this->provider = $provider;
    }

}
