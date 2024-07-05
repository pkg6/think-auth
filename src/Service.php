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

use tp5er\think\auth\commands\CreateUserCommand;
use tp5er\think\auth\commands\KeyGenerateCommand;
use tp5er\think\auth\commands\MakePolicyCommand;
use tp5er\think\auth\commands\MigrateAccessTokenCommand;
use tp5er\think\auth\commands\MigrateUserCommand;
use tp5er\think\auth\contracts\Authenticatable as AuthenticatableContract;
use tp5er\think\auth\contracts\Factory;
use tp5er\think\auth\contracts\Guard as ContractGuard;

class Service extends \think\Service
{
    protected $services = [
        \tp5er\think\auth\keyparser\AppService::class,
        \tp5er\think\auth\access\AppService::class,
        \tp5er\think\auth\sanctum\AppService::class,
        \tp5er\think\auth\jwt\AppService::class,
    ];

    /**
     * @var string[]
     */
    protected $commands = [
        MigrateUserCommand::class,
        MigrateAccessTokenCommand::class,

        CreateUserCommand::class,
        MakePolicyCommand::class,

        KeyGenerateCommand::class,
    ];
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'app\model\Model' => 'app\policies\ModelPolicy',
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
        $this->registerUserResolver();
        $this->registerAuthenticator();
        $this->registerRequest();
        $this->registerMiddleware();
        $this->serviceBind();
        $this->registerPolicies();
    }

    /**
     * @return void
     */
    protected function registerMiddleware()
    {
        // 自动启动session中间件
        $middlewares = $this->config('middleware.global', []);
        foreach ($middlewares as $middleware) {
            $this->app->middleware->add($middleware);
        }
        $this->app->config->set([
            'alias' => array_merge(
                $this->app->config->get('middleware.alias', []),
                $this->config('middleware.alias', [])
            )
        ], 'middleware');
    }

    /**
     * AppService the authenticator services.
     *
     * @return void
     */
    protected function registerAuthenticator()
    {
        $this->app->bind(Factory::class, function () {
            return new AuthManager($this->app);
        });

        $this->app->bind(ContractGuard::class, function () {
            return $this->app->get(Factory::class)->guard();
        });
    }

    protected function registerRequest()
    {
        $this->app->bind(\tp5er\think\auth\contracts\Request::class, Request::class);
        setRequestUserResolver($this->app->get(AuthenticatableContract::class));
    }

    /**
     * AppService a resolver for the authenticated user.
     *
     * @return void
     */
    protected function registerUserResolver()
    {
        $this->app->bind(AuthenticatableContract::class, function () {
            return $this->app->get(Factory::class)->userResolver();
        });
    }

    protected function serviceBind()
    {
        foreach ($this->services as $register) {
            if (class_exists($register)) {
                if (method_exists($register, 'bind') && method_exists($register, 'name')) {
                    $register::bind($this->app, $this->config($register::name(), []));
                }
            }
        }
    }

    /**
     * @return void
     */
    protected function registerPolicies()
    {
        \tp5er\think\auth\access\AppService::registerPolicy($this->app, $this->policies());
    }

    /**
     * Get the policies defined on the provider.
     *
     * @return array
     */
    public function policies()
    {
        return array_merge(
            $this->policies,
            $this->config('policies', [])
        );
    }

    /**
     * @param $key
     * @param $default
     *
     * @return array
     */
    protected function config($key, $default = null)
    {
        if ($key == false) {
            return $this->app->config->get('auth', []);
        }

        return $this->app->config->get("auth." . $key, $default);
    }
}
