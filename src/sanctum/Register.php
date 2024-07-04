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

namespace tp5er\think\auth\sanctum;

use think\App;
use tp5er\think\auth\contracts\Factory;
use tp5er\think\auth\RequestGuard;

class Register extends \tp5er\think\auth\Register
{
    const sanctum = 'sanctum';

    public static $config = [
        'guard' => ['web'],
        'expiration' => null,
    ];

    public static function name()
    {
        return 'sanctum';
    }

    public static function bind(App $app, $config = [])
    {
        parent::bind($app, $config);

        $auth = $app->get(Factory::class);
        $auth->configMergeGuards('sanctum', [
            "driver" => 'sanctum',
            "provider" => null
        ]);
        $auth->extend(Register::sanctum, function (App $app, $name, $sanctumConfig) use (&$auth) {
            $expiration = self::getConfig('expiration');

            return new RequestGuard(
                new Guard($app, $auth, $expiration, $sanctumConfig['provider']),
                $app->request,
                $auth->createUserProvider($sanctumConfig['provider'] ?? null)
            );
        });
    }
}
