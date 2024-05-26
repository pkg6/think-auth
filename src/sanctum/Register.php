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
use think\helper\Arr;
use tp5er\think\auth\contracts\Factory;
use tp5er\think\auth\RequestGuard;

class Register
{
    const config = "sanctum";
    const  sanctum = 'sanctum';

    public static function bind(App $app, $appConfig = [])
    {
        $auth = $app->get(Factory::class);
        $auth->configMergeGuards('sanctum', [
            "driver" => 'sanctum',
            "provider" => null
        ]);
        $auth->extend(Register::sanctum, function (App $app, $name, $config) use (&$auth, $appConfig) {
            $expiration = Arr::get($appConfig, 'expiration');

            return new RequestGuard(
                new Guard($app, $auth, $expiration, $config['provider']),
                $app->request,
                $auth->createUserProvider($config['provider'] ?? null)
            );
        });
    }
}
