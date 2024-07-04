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

abstract class Register
{
    public static $config = [];

    public static function name()
    {
        return 'auth';
    }

    /**
     * @param array $config
     *
     * @return array
     */
    public static function mergeConfig(array $config = [])
    {
        self::$config = array_merge(self::$config, $config);

        return self::$config;
    }

    /**
     * @param $key
     * @param $value
     *
     * @return void
     */
    public static function setConfig($key, $value)
    {
        Arr::set(self::$config, $key, $value);
        \app()->config->set([self::name() => self::$config], 'auth');
    }

    /**
     * @param $key
     * @param $default
     *
     * @return array|\ArrayAccess|mixed
     */
    public static function getConfig($key = null, $default = null)
    {
        if (is_null($key)) {
            return self::$config;
        }

        return Arr::get(self::$config, $key, $default);
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

    public static function bind(App $app, array $config = [])
    {
        self::mergeConfig($config);
    }
}
