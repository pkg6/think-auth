<?php


namespace tp5er\think\auth\Guards;

use InvalidArgumentException;
use think\facade\Db;
use think\helper\Arr;
use tp5er\think\auth\Model\User;
use tp5er\think\auth\UserProvider\DatabaseUserProvider;
use tp5er\think\auth\UserProvider\ModelUserProvider;
use tp5er\think\auth\UserProvider\UserProvider;

/**
 * Trait userProviderTrait
 * @package tp5er\think\auth\Guards
 */
trait userProviderTrait
{
    /**
     * Create the user provider implementation for the driver.
     * @param null $provider
     * @return UserProvider
     */
    public function createUserProvider($provider = null)
    {
        switch ($provider) {
            case 'model':
                return $this->createModelProvider();
                break;
            case "database":
                return $this->createDatabaseProvider();
                break;
            default:
                throw new InvalidArgumentException(
                    "Authentication user provider [{$provider}] is not defined."
                );
        }
    }

    /**
     * @return DatabaseUserProvider
     */
    protected function createDatabaseProvider()
    {
        $table   = Arr::get($this->config, 'provider.table', 'user');
        $connect = Arr::get($this->config, 'provider.connect', 'mysql');
        return new DatabaseUserProvider(Db::connect($connect), $table);
    }

    /**
     * @return ModelUserProvider
     */
    protected function createModelProvider()
    {
        $model = Arr::get($this->config, 'provider.model', User::class);
        return new ModelUserProvider($model);
    }


    /**
     * @return UserProvider
     */
    public function getUserProvider()
    {
        $driver = Arr::get($this->config, 'provider.driver', 'database');
        return $this->createUserProvider($driver);
    }
}