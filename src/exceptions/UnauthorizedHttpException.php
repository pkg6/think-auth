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

namespace tp5er\think\auth\exceptions;

use Exception;
use think\exception\HttpException;

class UnauthorizedHttpException extends HttpException
{
    public function __construct($challenge, $message = '', Exception $previous = null, array $headers = [], $code = 0)
    {
        $headers['WWW-Authenticate'] = $challenge;
        parent::__construct(401, $message, $previous, $headers, $code);
    }
}
