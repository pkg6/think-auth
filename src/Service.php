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
use tp5er\think\auth\commands\MakePolicyCommand;
use tp5er\think\auth\commands\MigrateAccessTokenCommand;
use tp5er\think\auth\commands\MigrateUserCommand;
use tp5er\think\auth\contracts\Factory;
use tp5er\think\auth\contracts\GateInterface;

class Service extends \think\Service
{

    /**
     * @var string[]
     */
    protected $commands = [
        MigrateUserCommand::class,
        MigrateAccessTokenCommand::class,

        CreateUserCommand::class,
        MakePolicyCommand::class
    ];

    /**
     * @return void
     */
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
        $this->registerMiddleware();

    }

    /**
     * @return void
     */
    protected function registerMiddleware()
    {
        // 自动启动session中间件
        $middlewares = $this->app->config->get("auth.middleware.global", []);
        foreach ($middlewares as $middleware) {
            $this->app->middleware->add($middleware);
        }

        $this->app->config->set([
            'alias' => array_merge(
                $this->app->config->get('middleware.alias', []),
                $this->app->config->get("auth.middleware.alias", [])
            )
        ], 'middleware');
    }

    /**
     * Register the authenticator services.
     *
     * @return void
     */
    protected function registerAuthenticator()
    {
        $this->app->bind(Factory::class, function () {
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

    /**
     * @return void
     */
    protected function registerAccessGate()
    {
        $this->app->bind(GateInterface::class, function () {
            return new Gate($this->app, function () {
                return call_user_func(auth()->userResolver());
            });
        });
    }

}
