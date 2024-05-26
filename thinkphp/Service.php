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

namespace tp5er\thinkphp\auth;

use tp5er\thinkphp\auth\commands\AccessCommand;
use tp5er\thinkphp\auth\commands\AuthCommand;
use tp5er\thinkphp\auth\commands\InitCommand;
use tp5er\thinkphp\auth\commands\JWTCommand;
use tp5er\thinkphp\auth\commands\SanctumCommand;

class Service extends \think\Service
{

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->commands([
            InitCommand::class,
            AuthCommand::class,
            SanctumCommand::class,
            AccessCommand::class,
            JWTCommand::class
        ]);
    }
}
