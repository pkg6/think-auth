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
use tp5er\think\auth\contracts\UserProvider;

trait CreatesUserProviders
{
    /**
     * The registered custom provider creators.
     *
     * @var Closure[]
     */
    protected $customProviderCreators = [];

    /**
     * Get the default user provider name.
     *
     * @return string
     */
    public function getDefaultUserProvider()
    {
        return $this->app->config->get('auth.defaults.provider');
    }

    /**
     * Register a custom provider creator Closure.
     *
     * @param string $name
     * @param Closure $callback
     *
     * @return $this
     */
    public function provider($name, Closure $callback)
    {
        $this->customProviderCreators[$name] = $callback;

        return $this;
    }

    /**
     * Create the user provider implementation for the driver.
     *
     * @param string|null $provider
     *
     * @return UserProvider|null
     *
     * @throws \InvalidArgumentException
     */
    public function createUserProvider($provider = null)
    {
        $config = $this->getProviderConfiguration($provider);
        if (is_null($config)) {
            return  null;
        }
        if (isset($this->customProviderCreators[$driver = ($config['driver'] ?? null)])) {
            return call_user_func(
                $this->customProviderCreators[$driver],
                $this->app,
                $config
            );
        }
        switch ($driver) {
            case 'database':
                return $this->createDatabaseProvider($config);
            case 'model':
            case 'eloquent':
                return $this->createEloquentProvider($config);
            default:
                throw new InvalidArgumentException(
                    "Authentication user provider [{$driver}] is not defined."
                );
        }
    }

    /**
     * Create an instance of the database user provider.
     *
     * @param array $config
     *
     * @return DatabaseUserProvider
     */
    protected function createDatabaseProvider($config)
    {
        $connection = $this->app->db->connect($config['connection'] ?? null);

        return new DatabaseUserProvider($connection, $this->app->get('hash'), $config['table']);
    }

    /**
     * Create an instance of the Eloquent user provider.
     *
     * @param array $config
     *
     * @return EloquentUserProvider
     */
    protected function createEloquentProvider($config)
    {
        return new EloquentUserProvider($this->app->get('hash'), $config['model']);
    }

    /**
     * Get the user provider configuration.
     *
     * @param string|null $provider
     *
     * @return array|null
     */
    protected function getProviderConfiguration($provider)
    {
        $provider = $provider ?: $this->getDefaultUserProvider();

        return $this->app->config->get('auth.providers.' . $provider);
    }

}
