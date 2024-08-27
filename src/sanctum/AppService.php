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

class AppService extends \tp5er\think\auth\AppService
{
    const sanctum = 'sanctum';

    public $config = [
        'guard' => ['web'],
        'expiration' => null,
    ];

    public static function name()
    {
        return 'sanctum';
    }

    public function bind()
    {
        $auth = $this->app->get(Factory::class);
        $auth->configMergeGuards('sanctum', [
            "driver" => 'sanctum',
            "provider" => null
        ]);
        $auth->extend(AppService::sanctum, function (App $app, $name, $sanctumConfig) use (&$auth) {
            $expiration = $this->getConfig('expiration');
            return new RequestGuard(
                new Guard($app, $auth, $expiration, $sanctumConfig['provider']),
                $app->request,
                $auth->createUserProvider($sanctumConfig['provider'] ?? null)
            );
        });
    }
}
