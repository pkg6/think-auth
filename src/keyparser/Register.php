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

use think\App;

class Register extends \tp5er\think\auth\Register
{
    const keyParser = "tp5er.auth.keyparser";

    public static $config = [
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
     * @param App $app
     * @param array $config
     *
     * @return void
     */
    public static function bind(App $app, array $config = [])
    {
        parent::bind($app, $config);
        array_walk(self::$config, function (&$classOrObject) {
            if (is_string($classOrObject)) {
                $classOrObject = new $classOrObject;
            }
        });
        $app->bind(Register::keyParser, function () use (&$app, &$config) {
            return new Factory($app->request, self::getConfig());
        });
        key_parser();
    }
}
