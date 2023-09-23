<?php

namespace tp5er\think\auth;

use tp5er\think\auth\Password\Password;


class Service extends \think\Service
{
    public function register()
    {

        //默认值设置
        $this->app->config->set(['password' => Password::class], 'auth');

        $this->app->bind('auth.password', function () {
            return new PasswordManager($this->app);
        });

        $this->app->bind('auth.guard', function () {
            return new AuthManager($this->app);
        });

        $this->app->bind('auth.jwt', function () {
            return new JwtAuthManager($this->app);
        });

        $this->app->bind('jwt.factory', function () {
            return new JwtFactory($this->app);
        });
    }
}