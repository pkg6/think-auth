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

use tp5er\think\auth\access\Gate;
use tp5er\think\auth\commands\CreateUserCommand;
use tp5er\think\auth\commands\MakePolicy;
use tp5er\think\auth\commands\MigrateCommand;
use tp5er\think\auth\contracts\GateInterface;

class Service extends \think\Service
{

    protected $commands = [
        MigrateCommand::class,
        CreateUserCommand::class,
        MakePolicy::class
    ];

    public function boot(): void
    {
        $this->commands($this->commands);
    }

    /**
     * @return void
     */
    public function register(): void
    {
        $this->registerAuthenticator();
        $this->registerUserResolver();
        $this->registerAccessGate();
    }

    /**
     * Register the authenticator services.
     *
     * @return void
     */
    protected function registerAuthenticator()
    {
        $this->app->bind('auth', function () {
            return new AuthManager($this->app);
        });

        $this->app->bind("auth.driver", function () {
            return auth()->guard();
        });
    }

    /**
     * Register a resolver for the authenticated user.
     *
     * @return void
     */
    protected function registerUserResolver()
    {
        $this->app->bind(Authenticatable::class, function () {
            return call_user_func(auth()->userResolver());
        });
    }
    protected function registerAccessGate()
    {
        $this->app->bind(GateInterface::class, function () {
            return new Gate($this->app, function () {
                return call_user_func(auth()->userResolver());
            });
        });
    }

}
