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
use tp5er\think\auth\commands\MakePolicyCommand;
use tp5er\think\auth\commands\MigrateAccessTokenCommand;
use tp5er\think\auth\commands\MigrateUserCommand;
use tp5er\think\auth\contracts\Authenticatable as AuthenticatableContract;
use tp5er\think\auth\contracts\Factory;
use tp5er\think\auth\contracts\Guard as ContractGuard;
use tp5er\think\auth\support\Ref;

class Service extends \think\Service
{
    protected $registers = [
        \tp5er\think\auth\keyparser\Register::class,
        \tp5er\think\auth\access\Register::class,
        \tp5er\think\auth\sanctum\Register::class,
        \tp5er\think\auth\access\Register::class,
        \tp5er\think\auth\jwt\Register::class,
    ];

    /**
     * @var string[]
     */
    protected $commands = [
        MigrateUserCommand::class,
        MigrateAccessTokenCommand::class,

        CreateUserCommand::class,
        MakePolicyCommand::class,
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
        $this->registerAuthenticator();
        $this->registerUserResolver();
        $this->registerMiddleware();
        $this->registers();
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
     * Register the authenticator services.
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

    /**
     * Register a resolver for the authenticated user.
     *
     * @return void
     */
    protected function registerUserResolver()
    {
        $this->app->bind(AuthenticatableContract::class, function () {
            return $this->app->get(Factory::class)->userResolver();
        });
    }

    protected function registers()
    {
        foreach ($this->registers as $register) {
            if (class_exists($register)) {
                $cfg = Ref::getClassConstValue($register, "config");
                if (method_exists($register, 'bind')) {
                    $register::bind($this->app, $this->config($cfg, []));
                }
            }
        }
    }

    /**
     * @return void
     */
    protected function registerPolicies()
    {
        \tp5er\think\auth\access\Register::registerPolicy($this->app, $this->policies());
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
