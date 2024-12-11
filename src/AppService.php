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
use think\helper\Arr;

abstract class AppService
{
    public $name = 'auth';
    public $config = [];
    /**
     * @var App
     */
    protected $app;

    public function __construct(App $app)
    {
        $this->mergeConfig($app);
    }

    public function mergeConfig(App $app)
    {
        $this->app = $app;
        $this->config = array_merge($this->config, $app->config->get($this->name, []));
    }

    /**
     * @param $key
     * @param $default
     *
     * @return array|\ArrayAccess|mixed
     */
    public function getConfig($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->config;
        }

        return Arr::get($this->config, $key, $default);
    }

    public static function getCfg($key = null, $default = null)
    {
        return (new static(\app()))->getConfig($key, $default);
    }

    /**
     * @param $service
     * @param $config
     *
     * @return void
     */
    public static function authSetConfig($service, $config)
    {
        \app()->config->set([$service => $config], 'auth');
    }

    /**
     * @param $key
     * @param $default
     *
     * @return array|mixed
     */
    public static function authGetConfig($key, $default = null)
    {
        return \app()->config->get('auth.' . $key, $default);
    }
}
