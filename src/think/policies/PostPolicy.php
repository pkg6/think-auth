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

namespace tp5er\think\auth\think\policies;

use tp5er\think\auth\contracts\Authenticatable;
use tp5er\think\auth\think\model\Post;

class PostPolicy
{
    public function create(Authenticatable $user, Post $post)
    {
        return true;
    }
}
