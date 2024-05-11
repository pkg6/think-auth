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

namespace tp5er\think\auth\sanctum\events;

use tp5er\think\auth\sanctum\PersonalAccessToken;

class TokenAuthenticated
{
    /**
     * The personal access token that was authenticated.
     *
     * @var PersonalAccessToken
     */
    public $token;

    /**
     * Create a new event instance.
     *
     * @param  PersonalAccessToken  $token
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }
}
