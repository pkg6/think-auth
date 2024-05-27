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

namespace tp5er\think\auth\contracts;

use think\Request;

interface KeyParserFactory
{
    /**
     * @param Request $request
     *
     * @return $this
     */
    public function setRequest(Request $request);

    /**
     * @return KeyParser[]
     */
    public function getParsers();

    /**
     * @param array $parser
     *
     * @return $this
     */
    public function setParsers(array $parser);

    /**
     * @return string|mixed
     */
    public function parseToken();

    /**
     * @return bool
     */
    public function hasToken();
}
