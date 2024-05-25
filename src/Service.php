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

use tp5er\think\auth\access\Gate;
use tp5er\think\auth\commands\CreateUserCommand;
use tp5er\think\auth\commands\MakePolicyCommand;
use tp5er\think\auth\commands\MigrateAccessTokenCommand;
use tp5er\think\auth\commands\MigrateUserCommand;
use tp5er\think\auth\contracts\Authenticatable as AuthenticatableContract;
use tp5er\think\auth\contracts\Factory;
use tp5er\think\auth\contracts\GateInterface;
use tp5er\think\auth\contracts\Guard as ContractGuard;
use tp5er\think\auth\contracts\KeyParserFactory;
use tp5er\think\auth\jwt\Blacklist as JWTBlacklist;
use tp5er\think\auth\jwt\contracts\JWT as JWTContract;
use tp5er\think\auth\jwt\JWTAuth;
use tp5er\think\auth\jwt\Manager as JWTManager;
use tp5er\think\auth\jwt\PayloadFactory as PayloadFactory;
use tp5er\think\auth\jwt\providers\jwt\Lcobucci;
use tp5er\think\auth\jwt\providers\storage\Think as JWTThinkStorage;
use tp5er\think\auth\sanctum\Guard;

class Service extends \think\Service
{

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
        $this->registerKeyParser();
        $this->registerAuthenticator();
        $this->registerUserResolver();
        $this->registerAccessGate();
        $this->registerMiddleware();
        $this->registerSanctum();
        $this->registerPolicies();
        $this->registerJWT();

    }

    /**
     * @return void
     */
    protected function registerKeyParser()
    {
        $this->app->bind(KeyParserFactory::class, function () {
            return new \tp5er\think\auth\keyparser\Factory($this->app->request);
        });
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

    /**
     * @return void
     */
    protected function registerAccessGate()
    {
        $this->app->bind(GateInterface::class, function () {
            return new Gate($this->app, $this->app->get(AuthenticatableContract::class));
        });
    }

    /**
     * @return void
     */
    protected function registerSanctum()
    {
        $auth = $this->app->get(Factory::class);
        $auth->configMergeGuards('sanctum', [
            "driver" => 'sanctum',
            "provider" => null
        ]);
        $auth->extend("sanctum", function (App $app, $name, $config) use (&$auth) {
            $expiration = $this->config('sanctum.expiration');

            return new RequestGuard(
                new Guard(
                    $this->app,
                    $auth,
                    $expiration,
                    $config['provider']
                ),
                $this->app->request,
                $auth->createUserProvider($config['provider'] ?? null)
            );
        });
    }

    /**
     * @return void
     */
    protected function registerPolicies()
    {
        foreach ($this->policies() as $key => $value) {
            $this->app->get(GateInterface::class)->policy($key, $value);
        }
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
     * @return void
     */
    protected function registerJWT()
    {
        /*** 定义的是jwt加密提供者 需要实现 JWTContract::clas 中的方法* @see  JWTContract::class */
        $this->app->bind(JWTContract::class, function () {
            $jwtClass = $this->config('jwt.providers.jwt');
            if (class_exists($jwtClass)) {
                return new $jwtClass(
                    $this->config('jwt.secret'),
                    $this->config('jwt.algo'),
                    $this->config('jwt.keys')
                );
            } else {
                return new Lcobucci(
                    $this->config('jwt.secret'),
                    $this->config('jwt.algo'),
                    $this->config('jwt.keys', [])
                );
            }
        });

        $this->app->bind(JWTBlacklist::class, function () {
            $storageClass = $this->config('jwt.providers.storage');
            if (class_exists($storageClass)) {
                $storage = new $storageClass($this->app);
            } else {
                $storage = new JWTThinkStorage($this->app);
            }

            return new JWTBlacklist($storage);
        });

        $this->app->bind(JWTManager::class, function () {
            $payloadFactory = new PayloadFactory($this->app);

            return new JWTManager(
                $this->app->get(JWTContract::class),
                $this->app->get(JWTBlacklist::class),
                $payloadFactory
            );
        });

        $this->app->bind(JWTAuth::class, function () {
            return new JWTAuth(
                $this->app->get(JWTManager::class),
                $this->app->get(KeyParserFactory::class)
            );
        });
    }

    /**
     * @param $key
     * @param $default
     *
     * @return array|mixed
     */
    protected function config($key, $default = null)
    {
        return $this->app->config->get("auth." . $key, $default);
    }

}
