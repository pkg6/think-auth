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

namespace tp5er\think\auth\support;

use think\Request;

class Req
{
    /**
     * @param Request $request
     * @return false|string|null
     */
    public static function bearerToken(Request $request)
    {
        $header = $request->header("Authorization", "");
        $position = strrpos($header, 'Bearer ');
        if ($position !== false) {
            $header = substr($header, $position + 7);

            return strpos($header, ',') !== false ? strstr($header, ',', true) : $header;
        }

        return null;
    }

    /**
     * @param Request $request
     * @return array|string|null
     */
    public static function getPassword(Request $request)
    {
        return $request->header('PHP_AUTH_PW');
    }
}
