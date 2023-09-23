<?php

namespace tp5er\think\auth;

use think\Manager;
use tp5er\think\auth\Contracts\Guard;
use tp5er\think\auth\Contracts\StatefulGuard;


class AuthManager extends Manager
{
    /**
     * @var string
     */
    protected $namespace = '\\tp5er\\think\\auth\\Guards\\';

    /**
     * @var string
     */
    protected $name;

    /**
     * @return string
     */
    public function getName(): string
    {
        $this->name = $this->app->config->get('auth.default');
        return $this->name;
    }

    /**
     * @return mixed|string|null
     */
    public function getDefaultDriver()
    {
        return $this->getName();
    }

    /**
     * 获取驱动类型
     * @param string $name
     * @return mixed
     */
    protected function resolveType(string $name)
    {
        return $this->app->config->get('auth.guards.' . $name . '.driver') . 'Guard';
    }

    /**
     * 获取驱动配置
     * @param string $name
     * @return mixed
     */
    protected function resolveConfig(string $name)
    {
        return $this->app->config->get('auth.guards.' . $name);
    }

    /**
     * @param null $name
     * @return Guard|StatefulGuard
     */
    public function guard($name = null)
    {
        $this->name = $name ?? $this->getName();
        return $this->driver($this->name);
    }
}