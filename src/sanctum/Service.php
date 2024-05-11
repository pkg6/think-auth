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

use tp5er\think\auth\RequestGuard;

class Service extends \think\Service
{
    public function register(): void
    {
        $guards = $this->app->config->get("auth.guards", []);

        $this->app->config->set([
            "guards" => array_merge($guards, [
                'sanctum' => [
                    "driver" => 'sanctum',
                    "provider" => null
                ]
            ]),
        ], "auth");
    }

    public function boot(): void
    {
        $this->configureGuard();
    }

    protected function configureGuard()
    {
        auth()->extend("sanctum", function (App $app, $name, $config) {
            $expiration = $this->app->config->get("auth.sanctum.expiration");

            return new RequestGuard(
                new Guard(auth(), $expiration, $config['provider']),
                $this->app->request,
                auth()->createUserProvider($config['provider'] ?? null)
            );
        });
    }
}
