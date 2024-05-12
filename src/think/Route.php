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

use think\facade\Route as thinkRoute;

class Route
{
    public static function api()
    {
        thinkRoute::get("/api/login", function () {
            auth()->attempt(["username" => "tp5er","password" => "123456"]);

            return json(['code' => 1,"msge" => "登录成功"]);
        });
        thinkRoute::get("/api/user", function () {
            return json(['code' => 0,"msg" => "获取登录信息","data" => requestUser()]);
        });
        thinkRoute::get("/api/token", function () {
            $token = requestUser()->createToken("test-token");

            return json(['code' => 0,"msg" => "获取token信息","data" => ["token" => $token->plainTextToken]]);
        });
        //curl -H "Authorization: Bearer 9|DqTQsBngTVJcFwJkslyvdZSeGuAjgaeikknQPHBI"  "http://127.0.0.1:8000/api/sanctum"
        thinkRoute::get("/api/sanctum", function () {
            return json(['code' => 0,"msg" => "通过sanctum获取用户信息","data" => requestUser()]);
        })->middleware('auth', "sanctum");
    }
}
