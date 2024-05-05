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

class Service extends \think\Service
{

    /**
     * @return void
     */
    public function register(): void
    {
        $this->registerAuthenticator();
        $this->registerUserResolver();
    }

    /**
     * Register the authenticator services.
     *
     * @return void
     */
    protected function registerAuthenticator()
    {
        $this->app->bind('auth', function () {
            return new AuthManager($this->app);
        });

        $this->app->bind("auth.driver", function () {
            return $this->app->get("auth")->guard();
        });
    }

    /**
     * Register a resolver for the authenticated user.
     *
     * @return void
     */
    protected function registerUserResolver()
    {
        $this->app->bind(Authenticatable::class, function () {
            return call_user_func($this->app->get("auth")->userResolver());
        });
    }

}
