<?php

namespace tp5er\think\auth\Http\Controller;

use tp5er\think\auth\JwtAuthManager;
use tp5er\think\auth\Traits\RequestToken;
use tp5er\think\auth\Traits\ResponseData;

/**
 * Trait Jwt
 * @package tp5er\think\auth\Http\Controller
 */
trait JwtGuard
{
    /**
     * @return JwtAuthManager
     */
    protected function jwt()
    {
        return app()->get('auth.jwt')->setAuth('web');
    }
}