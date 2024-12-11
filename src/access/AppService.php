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

namespace tp5er\think\auth\access;

use think\App;
use tp5er\think\auth\contracts\Authenticatable as AuthenticatableContract;
use tp5er\think\auth\contracts\GateInterface;

class AppService extends \tp5er\think\auth\AppService
{
    const abstract_gate = GateInterface::class;

    public $name = 'auth.access';

    public function bind()
    {
        $this->app->bind(AppService::abstract_gate, function () {
            return new Gate($this->app, $this->app->get(AuthenticatableContract::class));
        });
    }

    public static function registerPolicy(App $app, $policy = [])
    {
        foreach ($policy as $key => $value) {
            $app->get(AppService::abstract_gate)->policy($key, $value);
        }
    }
}
