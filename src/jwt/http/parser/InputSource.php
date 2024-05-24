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

namespace tp5er\think\auth\jwt\http\parser;

use think\Request;
use tp5er\think\auth\jwt\contracts\Parser as ParserContract;

class InputSource implements ParserContract
{
    use KeyTrait;

    /**
     * Try to parse the token from the request input source.
     *
     * @param  Request  $request
     *
     * @return null|string
     */
    public function parse(Request $request)
    {
        return $request->param([$this->key]);
    }
}
