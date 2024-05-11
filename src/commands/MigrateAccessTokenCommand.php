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

namespace tp5er\think\auth\commands;

class MigrateAccessTokenCommand extends MigrateAbstract
{

    protected $default_table = "personal_access_token";

    public function cmd()
    {
        return 'access-token';
    }

    /**
     * @return string
     */
    protected function stubs()
    {
        return 'personal_access_token.stub';
    }
}
