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

use Closure;
use InvalidArgumentException;
use think\App;
use tp5er\think\auth\contracts\AuthManagerInterface;
use tp5er\think\auth\contracts\Factory;
use tp5er\think\auth\contracts\Guard;
use tp5er\think\auth\contracts\StatefulGuard;
use tp5er\think\auth\jwt\Register as JWTRegister;

class AuthManager implements AuthManagerInterface, Factory
{
    use CreatesUserProviders;

    /**
     * @var App
     */
    protected $app;

    /**
     * The array of created "drivers".
     *
     * @var array
     */
    protected $guards = [];
    /**
     * The user resolver shared by various services.
     *
     * Determines the default user for Gate, Request, and the Authenticatable contract.
     *
     * @var \Closure
     */
    protected $userResolver;
    /**
     * The registered custom driver creators.
     *
     * @var array
     */
    protected $customCreators = [];

    /**
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->userResolver = function ($guard = null) {
            return $this->guard($guard)->user();
        };
    }

    /**
     * @inheritDoc
     */
    public function userResolver()
    {
        return $this->userResolver;
    }

    /**
     * @inheritDoc
     */
    public function resolveUsersUsing(Closure $userResolver)
    {
        $this->userResolver = $userResolver;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultDriver()
    {
        return $this->app->config->get("auth.defaults.guard");
    }

    /**
     * @inheritDoc
     */
    public function setDefaultDriver($name)
    {
        $this->app->config->set(["defaults" => ["guard" => $name]], "auth");

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function viaRequest($driver, callable $callback)
    {
        return $this->extend($driver, function () use ($callback) {
            $guard = new RequestGuard($callback, $this->app->request, $this->createUserProvider());

            return $guard;
        });
    }

    /**
     * @inheritDoc
     */
    public function setConfigGuardProvider($guard, $tableOrModel, $guardDriver = "session")
    {
        if (class_exists($tableOrModel)) {
            $provider = [
                'driver' => 'eloquent',
                'model' => $tableOrModel,
            ];
        } else {
            $provider = [
                'driver' => 'database',
                'table' => $tableOrModel,
            ];
        }
        $this->configMergeGuards($guard, [
            "driver" => $guardDriver,
            "provider" => $guard
        ]);
        $this->configMergeProviders($guard, $provider);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function configMergeGuards($guard, $config)
    {
        $this->app->config->set([
            "guards" => array_merge(
                $this->app->config->get("auth.guards", []),
                [$guard => $config]
            ),
        ], "auth");

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function configMergeProviders($guard, $config)
    {
        $this->app->config->set([
            "providers" => array_merge(
                $this->app->config->get("auth.providers", []),
                [$guard => $config]
            ),
        ], "auth");

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function extend($driver, Closure $callback)
    {
        $this->customCreators[$driver] = $callback;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function guard($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        return $this->guards[$name] ?? $this->guards[$name] = $this->resolve($name);
    }

    /**
     * @inheritDoc
     */
    public function hasResolvedGuards()
    {
        return count($this->guards) > 0;
    }

    /**
     * @inheritDoc
     */
    public function forgetGuards()
    {
        $this->guards = [];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function shouldUse($name)
    {
        $name = $name ?: $this->getDefaultDriver();
        $this->setDefaultDriver($name);
        $this->userResolver = function ($name = null) {
            return $this->guard($name)->user();
        };
    }

    /**
     * @inheritDoc
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Auth guard [{$name}] is not defined.");
        }
        if (isset($this->customCreators[$config['driver']])) {
            return $this->callCustomCreator($name, $config);
        }
        $driverMethod = 'create' . ucfirst($config['driver']) . 'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($name, $config);
        }
        throw new InvalidArgumentException(
            "Auth driver [{$config['driver']}] for guard [{$name}] is not defined."
        );
    }

    /**
     * Create a session based authentication guard.
     *
     * @param string $name
     * @param array $config
     *
     * @return SessionGuard
     */
    protected function createSessionDriver($name, $config)
    {
        $provider = $this->createUserProvider($config['provider'] ?? null);

        $guard = new SessionGuard(
            $this->app,
            $name,
            $provider
        );
        // When using the remember me functionality of the authentication services we
        // will need to be set the encryption instance of the guard, which allows
        // secure, encrypted cookie values to get generated for those cookies.
        if (method_exists($guard, 'setCookie')) {
            $guard->setCookie($this->app->cookie);
        }
        if (isset($config['remember'])) {
            $guard->setRememberDuration($config['remember']);
        }
        if (method_exists($guard, 'setRequest')) {
            $guard->setRequest($this->app->request);
        }

        return $guard;
    }

    /**
     * Create a token based authentication guard.
     *
     * @param string $name
     * @param array $config
     *
     * @return JWTGuard
     */
    protected function createJWTDriver($name, $config)
    {
        $provider = $this->createUserProvider($config['provider'] ?? null);
        $guard = new JWTGuard(
            $this->app,
            $name,
            $provider,
            $this->app->get(JWTRegister::auth)
        );

        return $guard;
    }

    /**
     * Create a token based authentication guard.
     *
     * @param string $name
     * @param array $config
     *
     * @return TokenGuard
     */
    protected function createTokenDriver($name, $config)
    {
        // The token guard implements a basic API token based guard implementation
        // that takes an API token field from the request and matches it to the
        // user in the database or another persistence layer where users are.
        $guard = new TokenGuard(
            $this->createUserProvider($config['provider'] ?? null),
            $this->app->request,
            $config['input_key'] ?? 'api_token',
            $config['storage_key'] ?? 'api_token',
            $config['hash'] ?? false
        );

        return $guard;
    }

    /**
     * Get the guard configuration.
     *
     * @param string $name
     *
     * @return array
     */
    protected function getConfig($name)
    {
        return $this->app->config->get("auth.guards.{$name}");
    }

    /**
     * Call a custom driver creator.
     *
     * @param string $name
     * @param array $config
     *
     * @return mixed
     */
    protected function callCustomCreator($name, array $config)
    {
        return $this->customCreators[$config['driver']]($this->app, $name, $config);
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param string $method
     * @param array $parameters
     *
     * @return Guard|StatefulGuard
     */
    public function __call($method, $parameters)
    {
        return $this->guard()->{$method}(...$parameters);
    }
}
