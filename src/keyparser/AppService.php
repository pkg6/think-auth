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

namespace tp5er\think\auth\keyparser;

class AppService extends \tp5er\think\auth\AppService
{
    const keyParser = "tp5er.auth.keyparser";

    public $config = [
        AuthHeaders::class,
        QueryString::class,
        InputSource::class,
        RouteParams::class,
        Cookies::class,
    ];

    public static function name()
    {
        return 'keyparser';
    }

    /**
     * @return void
     */
    public function bind()
    {
        $cfg = $this->getConfig();
        array_walk($cfg, function (&$classOrObject) {
            if (class_exists($classOrObject)) {
                $classOrObject = new $classOrObject;
            }
        });
        $this->app->bind(AppService::keyParser, function () use (&$cfg) {
            return new Factory($this->app->request, $cfg);
        });
    }
}
