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

namespace tp5er\think\auth\jwt\contracts;

use think\Request;

interface Parser
{

    /**
     * Parse the request.
     *
     * @param  Request  $request
     *
     * @return null|string
     */
    public function parse(Request $request);
}
