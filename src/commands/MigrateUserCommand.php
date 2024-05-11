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

class MigrateUserCommand extends MigrateAbstract
{

    protected $default_table = "user";

    public function cmd()
    {
        return parent::cmd();
    }

    /**
     * @return string
     */
    protected function stubs()
    {
        return 'user.stub';
    }

}
