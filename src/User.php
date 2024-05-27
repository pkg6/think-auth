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

namespace tp5er\think\auth;

use think\Model;
use tp5er\think\auth\access\Authorizable;
use tp5er\think\auth\Contracts\Authenticatable as AuthenticatableContract;
use tp5er\think\auth\Contracts\Authorizable as AuthorizableContract;
use tp5er\think\auth\jwt\contracts\JWTSubject as JWTSubjectContract;
use tp5er\think\auth\jwt\JWTSubject;
use tp5er\think\auth\sanctum\HasApiTokens;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubjectContract
{
    use Authenticatable,HasApiTokens,Authorizable,JWTSubject;
}
