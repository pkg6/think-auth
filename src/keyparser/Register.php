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
use think\helper\Arr;

class Register
{
    const config = "parser";
    const keyParser = "tp5er.auth.keyparser";

    /**
     * @return array
     */
    public static function defaultParsers()
    {
        return [
            new AuthHeaders,
            new QueryString,
            new InputSource,
            new RouteParams,
            new Cookies(),
        ];
    }

    /**
     * @param App $app
     * @param array $config
     *
     * @return void
     */
    public static function bind(App $app, array $config = [])
    {
        $app->bind(Register::keyParser, function () use (&$app, &$config) {
            return new Factory($app->request, Arr::get($config, 'parsers', Register::defaultParsers()));
        });
        key_parser();
    }
}
