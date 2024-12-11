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

namespace tp5er\think\auth\think;

use tp5er\think\auth\think\commands\AccessCommand;
use tp5er\think\auth\think\commands\AuthCommand;
use tp5er\think\auth\think\commands\CasbinCommand;
use tp5er\think\auth\think\commands\InitCommand;
use tp5er\think\auth\think\commands\JWTCommand;
use tp5er\think\auth\think\commands\SanctumCommand;

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
            JWTCommand::class,
            CasbinCommand::class,
        ]);
    }
}
